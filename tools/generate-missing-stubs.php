<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$repoRoot = dirname($root, 2);
$reference = $argv[1] ?? $repoRoot . '/refs/pocketmine/PocketMine-MP/src';
$facade = $argv[2] ?? $root . '/src';

if (!is_dir($reference) || !is_dir($facade)) {
    fwrite(STDERR, "usage: generate-missing-stubs.php [reference-src] [facade-src]\n");
    exit(2);
}

$referenceApi = scanApi($reference);
$facadeApi = scanApi($facade);
$generated = 0;

foreach ($referenceApi as $class => $shape) {
    if (!str_starts_with($class, 'pocketmine\\')) {
        continue;
    }
    if (isset($facadeApi[$class]) && !isGeneratedStub($facade, $class)) {
        continue;
    }
    writeStub($facade, $class, $shape);
    $generated++;
}

function isGeneratedStub(string $facade, string $class): bool
{
    $relative = substr($class, strlen('pocketmine\\'));
    $path = $facade . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';
    return is_file($path) && str_contains((string) file_get_contents($path), 'Generated PMMP compatibility stub.');
}

echo "generated {$generated} stubs\n";

/** @return array<string, array{type: string, methods: array<string, array{static: bool}>, constants: array<string, true>}> */
function scanApi(string $dir): array
{
    $api = [];
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($files as $file) {
        if (!$file instanceof SplFileInfo || !$file->isFile() || $file->getExtension() !== 'php') {
            continue;
        }
        foreach (scanFile($file->getPathname()) as $class => $shape) {
            $api[$class] = $shape;
        }
    }
    ksort($api);
    return $api;
}

/** @return array<string, array{type: string, methods: array<string, array{static: bool}>, constants: array<string, true>}> */
function scanFile(string $path): array
{
    $tokens = token_get_all((string) file_get_contents($path));
    $namespace = '';
    $classes = [];
    $count = count($tokens);
    for ($i = 0; $i < $count; $i++) {
        $token = $tokens[$i];
        if (!is_array($token)) {
            continue;
        }
        if ($token[0] === T_NAMESPACE) {
            $namespace = readNamespace($tokens, $i + 1);
            continue;
        }
        if (!in_array($token[0], [T_CLASS, T_INTERFACE, T_TRAIT, T_ENUM], true)) {
            continue;
        }
        if (previousMeaningfulToken($tokens, $i) === T_NEW) {
            continue;
        }
        $name = readNextIdentifier($tokens, $i + 1);
        if ($name === null) {
            continue;
        }
        $bodyStart = findNextChar($tokens, $i, '{');
        $bodyEnd = $bodyStart === null ? null : findMatchingBrace($tokens, $bodyStart);
        if ($bodyStart === null || $bodyEnd === null) {
            continue;
        }
        $classes[ltrim($namespace . '\\' . $name, '\\')] = [
            'type' => token_name($token[0]),
            'methods' => scanClassMethods($tokens, $bodyStart + 1, $bodyEnd),
            'constants' => scanClassConstants($tokens, $bodyStart + 1, $bodyEnd),
        ];
        $i = $bodyEnd;
    }
    return $classes;
}

/** @param array<int, mixed> $tokens */
function readNamespace(array $tokens, int $start): string
{
    $parts = [];
    for ($i = $start, $count = count($tokens); $i < $count; $i++) {
        $token = $tokens[$i];
        if ($token === ';' || $token === '{') {
            break;
        }
        if (is_array($token) && in_array($token[0], [T_STRING, T_NAME_QUALIFIED, T_NS_SEPARATOR], true)) {
            $parts[] = $token[1];
        }
    }
    return implode('', $parts);
}

/** @param array<int, mixed> $tokens */
function readNextIdentifier(array $tokens, int $start): ?string
{
    for ($i = $start, $count = count($tokens); $i < $count; $i++) {
        $token = $tokens[$i];
        if (is_array($token) && $token[0] === T_STRING) {
            return $token[1];
        }
        if ($token === '{' || $token === ';') {
            return null;
        }
    }
    return null;
}

/** @param array<int, mixed> $tokens */
function previousMeaningfulToken(array $tokens, int $offset): ?int
{
    for ($i = $offset - 1; $i >= 0; $i--) {
        $token = $tokens[$i];
        if (is_array($token) && in_array($token[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
            continue;
        }
        return is_array($token) ? $token[0] : null;
    }
    return null;
}

/** @param array<int, mixed> $tokens */
function findNextChar(array $tokens, int $start, string $char): ?int
{
    for ($i = $start, $count = count($tokens); $i < $count; $i++) {
        if ($tokens[$i] === $char) {
            return $i;
        }
    }
    return null;
}

/** @param array<int, mixed> $tokens */
function findMatchingBrace(array $tokens, int $start): ?int
{
    $depth = 0;
    for ($i = $start, $count = count($tokens); $i < $count; $i++) {
        if ($tokens[$i] === '{') {
            $depth++;
        } elseif ($tokens[$i] === '}') {
            $depth--;
            if ($depth === 0) {
                return $i;
            }
        }
    }
    return null;
}

/** @param array<int, mixed> $tokens @return array<string, array{static: bool}> */
function scanClassMethods(array $tokens, int $start, int $end): array
{
    $methods = [];
    $depth = 0;
    $public = false;
    $static = false;
    for ($i = $start; $i < $end; $i++) {
        $token = $tokens[$i];
        if ($token === '{') {
            $depth++;
            continue;
        }
        if ($token === '}') {
            $depth--;
            continue;
        }
        if ($depth !== 0 || !is_array($token)) {
            continue;
        }
        if ($token[0] === T_PUBLIC) {
            $public = true;
            continue;
        }
        if ($token[0] === T_STATIC && $public) {
            $static = true;
            continue;
        }
        if (in_array($token[0], [T_PRIVATE, T_PROTECTED], true)) {
            $public = false;
            $static = false;
            continue;
        }
        if ($token[0] === T_FUNCTION) {
            $name = readNextIdentifier($tokens, $i + 1);
            if ($name !== null && $public) {
                $methods[$name] = ['static' => $static];
            }
            $public = false;
            $static = false;
        }
    }
    ksort($methods);
    return $methods;
}

/** @param array<int, mixed> $tokens @return array<string, true> */
function scanClassConstants(array $tokens, int $start, int $end): array
{
    $constants = [];
    $depth = 0;
    $public = false;
    for ($i = $start; $i < $end; $i++) {
        $token = $tokens[$i];
        if ($token === '{') {
            $depth++;
            continue;
        }
        if ($token === '}') {
            $depth--;
            continue;
        }
        if ($depth !== 0 || !is_array($token)) {
            continue;
        }
        if ($token[0] === T_PUBLIC) {
            $public = true;
            continue;
        }
        if (in_array($token[0], [T_PRIVATE, T_PROTECTED], true)) {
            $public = false;
            continue;
        }
        if ($token[0] === T_CONST && $public) {
            $name = readNextIdentifier($tokens, $i + 1);
            if ($name !== null) {
                $constants[$name] = true;
            }
            $public = false;
        }
    }
    ksort($constants);
    return $constants;
}

/** @param array{type: string, methods: array<string, array{static: bool}>, constants: array<string, true>} $shape */
function writeStub(string $facade, string $class, array $shape): void
{
    $relative = substr($class, strlen('pocketmine\\'));
    $path = $facade . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $lastSlash = strrpos($class, '\\');
    $namespace = substr($class, 0, $lastSlash);
    $short = substr($class, $lastSlash + 1);
    $kind = match ($shape['type']) {
        'T_INTERFACE' => 'interface',
        'T_TRAIT' => 'trait',
        'T_ENUM' => 'enum',
        default => 'class',
    };

    $extends = '';
    if ($kind === 'class') {
        if (str_starts_with($class, 'pocketmine\\block\\')) {
            $extends = ' extends \\pocketmine\\block\\Block';
        } elseif (str_starts_with($class, 'pocketmine\\item\\')) {
            $extends = ' extends \\pocketmine\\item\\Item';
        } elseif (str_starts_with($class, 'pocketmine\\event\\')) {
            $extends = ' extends \\pocketmine\\event\\Event';
        }
    }

    $code = "<?php\n\n";
    $code .= "declare(strict_types=1);\n\n";
    $code .= "namespace {$namespace};\n\n";
    $code .= "/**\n * Generated PMMP compatibility stub.\n * Replace with a handwritten bridge facade when behavior matters.\n */\n";
    $code .= "{$kind} {$short}{$extends}\n{\n";
    if ($kind === 'enum') {
        $code .= "    case STUB;\n";
    }
    if ($kind === 'class' && $extends === ' extends \\pocketmine\\block\\Block') {
        $code .= "    public function __construct(mixed ...\$args) { parent::__construct('minecraft:" . strtolower($short) . "', '{$short}'); }\n";
    } elseif ($kind === 'class' && $extends === ' extends \\pocketmine\\item\\Item') {
        $code .= "    public function __construct(mixed ...\$args) { parent::__construct('minecraft:" . strtolower($short) . "', '{$short}'); }\n";
    }
    foreach (array_keys($shape['constants']) as $constant) {
        $code .= "    public const {$constant} = 0;\n";
    }
    foreach ($shape['methods'] as $method => $meta) {
        if ($method === '__construct' && in_array($extends, [' extends \\pocketmine\\block\\Block', ' extends \\pocketmine\\item\\Item'], true)) {
            continue;
        }
        $static = $meta['static'] ? ' static' : '';
        if ($kind === 'interface') {
            if ($method === '__construct' || $method === '__destruct' || $method === '__clone' || $method === '__wakeup') {
                $code .= "    public{$static} function {$method}(mixed ...\$args);\n";
            } elseif ($method === '__toString') {
                $code .= "    public{$static} function __toString(): string;\n";
            } elseif ($method === '__debugInfo' || $method === '__serialize' || $method === '__sleep') {
                $code .= "    public{$static} function {$method}(mixed ...\$args): array;\n";
            } elseif ($method === '__unserialize') {
                $code .= "    public{$static} function __unserialize(array \$data): void;\n";
            } elseif ($method === '__isset') {
                $code .= "    public{$static} function __isset(string \$name): bool;\n";
            } elseif ($method === '__set') {
                $code .= "    public{$static} function __set(string \$name, mixed \$value): void;\n";
            } elseif ($method === '__get') {
                $code .= "    public{$static} function __get(string \$name): mixed;\n";
            } elseif ($method === '__call') {
                $code .= "    public function __call(string \$name, array \$arguments): mixed;\n";
            } elseif ($method === '__callStatic') {
                $code .= "    public static function __callStatic(string \$name, array \$arguments): mixed;\n";
            } else {
                $code .= "    public{$static} function {$method}(mixed ...\$args): mixed;\n";
            }
            continue;
        }
        if ($method === '__destruct') {
            $code .= "    public function __destruct() {}\n";
        } elseif ($method === '__toString') {
            $code .= "    public{$static} function __toString(): string { return static::class; }\n";
        } elseif ($method === '__debugInfo') {
            $code .= "    public{$static} function __debugInfo(): array { return []; }\n";
        } elseif ($method === '__serialize') {
            $code .= "    public{$static} function __serialize(): array { return []; }\n";
        } elseif ($method === '__unserialize') {
            $code .= "    public{$static} function __unserialize(array \$data): void {}\n";
        } elseif ($method === '__sleep') {
            $code .= "    public{$static} function __sleep(): array { return []; }\n";
        } elseif ($method === '__wakeup') {
            $code .= "    public{$static} function __wakeup(): void {}\n";
        } elseif ($method === '__isset') {
            $code .= "    public{$static} function __isset(string \$name): bool { return false; }\n";
        } elseif ($method === '__set') {
            $code .= "    public{$static} function __set(string \$name, mixed \$value): void {}\n";
        } elseif ($method === '__get') {
            $code .= "    public{$static} function __get(string \$name): mixed { return null; }\n";
        } elseif ($method === '__call') {
            $code .= "    public function __call(string \$name, array \$arguments): mixed { return null; }\n";
        } elseif ($method === '__callStatic') {
            $code .= "    public static function __callStatic(string \$name, array \$arguments): mixed { return null; }\n";
        } elseif ($method === '__clone') {
            $code .= "    public function __clone() {}\n";
        } elseif ($method === '__invoke') {
            $code .= "    public{$static} function __invoke(mixed ...\$args): mixed { return null; }\n";
        } elseif ($method === '__construct') {
            $code .= "    public function __construct(mixed ...\$args) {}\n";
        } else {
            $code .= "    public{$static} function {$method}(mixed ...\$args): mixed { return null; }\n";
        }
    }
    $code .= "}\n";

    file_put_contents($path, $code);
}
