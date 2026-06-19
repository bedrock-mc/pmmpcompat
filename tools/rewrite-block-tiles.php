<?php

declare(strict_types=1);

$dir = __DIR__ . '/../src/block/tile';

foreach (glob($dir . '/*.php') as $file) {
    $code = file_get_contents($file);
    if ($code === false || !str_contains($code, 'Generated PMMP compatibility stub.')) {
        continue;
    }

    preg_match('/namespace\s+([^;]+);/', $code, $nsMatch);
    $namespace = $nsMatch[1] ?? 'pocketmine\\block\\tile';
    preg_match('/\b(class|interface|trait)\s+(\w+)/', $code, $typeMatch);
    if ($typeMatch === []) {
        continue;
    }

    $type = $typeMatch[1];
    $name = $typeMatch[2];
    preg_match_all('/^\s*public\s+const\s+[^;]+;/m', $code, $constMatches);
    $constants = $constMatches[0] ?? [];
    preg_match_all('/public\s+(static\s+)?function\s+(\w+)\s*\([^)]*\)/', $code, $methodMatches, PREG_SET_ORDER);

    $out = "<?php\n\n";
    $out .= "declare(strict_types=1);\n\n";
    $out .= "namespace " . $namespace . ";\n\n";

    if ($type === 'interface') {
        $out .= "interface " . $name . "\n{\n";
        foreach ($constants as $constant) {
            $out .= "    " . trim($constant) . "\n";
        }
        foreach ($methodMatches as $method) {
            $static = $method[1] !== '' ? 'static ' : '';
            $out .= "    public " . $static . "function " . $method[2] . "(mixed ...\$args): mixed;\n";
        }
        $out .= "}\n";
    } elseif ($type === 'trait') {
        $out .= "trait " . $name . "\n{\n";
        foreach ($methodMatches as $method) {
            $static = $method[1] !== '' ? 'static ' : '';
            $methodName = $method[2];
            if ($method[1] !== '') {
                $out .= "    public static function " . $methodName . "(mixed ...\$args): mixed { return null; }\n";
            } else {
                $out .= "    public function " . $methodName . "(mixed ...\$args): mixed { return method_exists(\$this, 'compatTileMethod') ? \$this->compatTileMethod(__FUNCTION__, \$args) : null; }\n";
            }
        }
        $out .= "}\n";
    } elseif ($name === 'TileFactory') {
        $out .= "class TileFactory\n{\n";
        foreach ($constants as $constant) {
            $out .= "    " . trim($constant) . "\n";
        }
        $out .= "    public function __construct(mixed ...\$args) {}\n";
        foreach ($methodMatches as $method) {
            $methodName = $method[2];
            if ($methodName === '__construct') {
                continue;
            }
            $out .= "    public static function " . $methodName . "(mixed ...\$args): mixed { return Tile::compatTileStaticMethod(__FUNCTION__, \$args); }\n";
        }
        $out .= "}\n";
    } else {
        $out .= "class " . $name . " extends Tile\n{\n";
        $out .= "    public function __construct(mixed ...\$args) { parent::__construct(...\$args); }\n";
        foreach ($constants as $constant) {
            $out .= "    " . trim($constant) . "\n";
        }
        foreach ($methodMatches as $method) {
            $methodName = $method[2];
            if ($methodName === '__construct') {
                continue;
            }
            if ($method[1] !== '') {
                $out .= "    public static function " . $methodName . "(mixed ...\$args): mixed { return self::compatTileStaticMethod(__FUNCTION__, \$args); }\n";
            } else {
                $out .= "    public function " . $methodName . "(mixed ...\$args): mixed { return \$this->compatTileMethod(__FUNCTION__, \$args); }\n";
            }
        }
        $out .= "}\n";
    }

    file_put_contents($file, $out);
}
