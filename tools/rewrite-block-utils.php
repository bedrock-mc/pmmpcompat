<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$dir = $root . '/src/block/utils';
$refDir = dirname($root, 2) . '/refs/pocketmine/PocketMine-MP/src/block/utils';

foreach (glob($dir . '/*.php') as $file) {
    $src = (string) file_get_contents($file);
    if (!str_contains($src, 'Generated PMMP compatibility stub.')) {
        continue;
    }
    preg_match('/(class|trait|interface|enum)\s+(\w+)/', $src, $kindMatch);
    $kind = $kindMatch[1] ?? null;
    $name = $kindMatch[2] ?? null;
    if ($kind === null || $name === null) {
        continue;
    }
    preg_match_all('/public (static )?function ([A-Za-z0-9_]+)\(/', $src, $methodMatches, PREG_SET_ORDER);
    $methods = [];
    foreach ($methodMatches as $match) {
        $methods[] = ['name' => $match[2], 'static' => trim($match[1]) === 'static'];
    }
    preg_match_all('/public const ([A-Z0-9_]+)/', $src, $constMatches);
    $constants = $constMatches[1] ?? [];

    $code = "<?php\n\ndeclare(strict_types=1);\n\nnamespace pocketmine\\block\\utils;\n\n";
    if ($kind === 'enum') {
        $cases = enumCases($refDir . '/' . $name . '.php');
        $code .= "use pocketmine\\color\\Color;\nuse pocketmine\\utils\\LegacyEnumShimTrait;\n\n";
        $code .= "enum {$name}\n{\n    use LegacyEnumShimTrait;\n\n";
        foreach ($cases as $case) {
            $code .= "    case {$case};\n";
        }
        foreach ($methods as $method) {
            $code .= "\n" . enumMethod($name, $method['name']);
        }
        $code .= "}\n";
    } elseif ($kind === 'interface') {
        $code .= "interface {$name}\n{\n";
        foreach ($constants as $constant) {
            $code .= "    public const {$constant} = 0;\n";
        }
        foreach ($methods as $method) {
            $static = $method['static'] ? ' static' : '';
            $code .= "    public{$static} function {$method['name']}(mixed ...\$args): mixed;\n";
        }
        $code .= "}\n";
    } elseif ($kind === 'trait') {
        $code .= "trait {$name}\n{\n";
        foreach ($methods as $method) {
            $code .= traitMethod($method['name']);
        }
        $code .= "}\n";
    } else {
        $code .= "class {$name}\n{\n";
        foreach ($constants as $constant) {
            $code .= "    public const {$constant} = 0;\n";
        }
        $code .= "    /** @var list<mixed> */\n    private array \$args;\n\n";
        $code .= "    public function __construct(mixed ...\$args)\n    {\n        \$this->args = \$args;\n    }\n";
        foreach ($methods as $method) {
            if ($method['name'] === '__construct') {
                continue;
            }
            $code .= classMethod($name, $method['name'], $method['static']);
        }
        $code .= "}\n";
    }
    file_put_contents($file, $code);
}

/** @return list<string> */
function enumCases(string $path): array
{
    if (!is_file($path)) {
        return ['STUB'];
    }
    preg_match_all('/case\s+([A-Z0-9_]+)/', (string) file_get_contents($path), $matches);
    return $matches[1] !== [] ? $matches[1] : ['STUB'];
}

function enumMethod(string $enum, string $name): string
{
    return match ($name) {
        'getDisplayName' => "    public function getDisplayName(mixed ...\$args): mixed { return ucwords(strtolower(str_replace('_', ' ', \$this->name))); }\n",
        'getRgbValue' => "    public function getRgbValue(mixed ...\$args): mixed { return match(\$this->name) {\n        'WHITE' => new Color(0xf0, 0xf0, 0xf0),\n        'ORANGE' => new Color(0xf9, 0x80, 0x1d),\n        'MAGENTA' => new Color(0xc7, 0x4e, 0xbd),\n        'LIGHT_BLUE' => new Color(0x3a, 0xb3, 0xda),\n        'YELLOW' => new Color(0xfe, 0xd8, 0x3d),\n        'LIME' => new Color(0x80, 0xc7, 0x1f),\n        'PINK' => new Color(0xf3, 0x8b, 0xaa),\n        'GRAY' => new Color(0x47, 0x4f, 0x52),\n        'LIGHT_GRAY' => new Color(0x9d, 0x9d, 0x97),\n        'CYAN' => new Color(0x16, 0x9c, 0x9c),\n        'PURPLE' => new Color(0x89, 0x32, 0xb8),\n        'BLUE' => new Color(0x3c, 0x44, 0xaa),\n        'BROWN' => new Color(0x83, 0x54, 0x32),\n        'GREEN' => new Color(0x5e, 0x7c, 0x16),\n        'RED' => new Color(0xb0, 0x2e, 0x26),\n        'BLACK' => new Color(0x1d, 0x1d, 0x21),\n        default => new Color(0xff, 0xff, 0xff),\n    }; }\n",
        'hasCenterSupport' => "    public function hasCenterSupport(mixed ...\$args): mixed { return \$this->name === 'CENTER' || \$this->name === 'FULL'; }\n",
        'hasEdgeSupport' => "    public function hasEdgeSupport(mixed ...\$args): mixed { return \$this->name === 'EDGE' || \$this->name === 'FULL'; }\n",
        'getSlotNumber' => "    public function getSlotNumber(mixed ...\$args): mixed { return array_search(\$this, self::cases(), true); }\n",
        'fromBlockFaceCoordinates' => "    public static function fromBlockFaceCoordinates(mixed ...\$args): mixed {\n        \$x = (float) (\$args[0] ?? 0.0);\n        \$y = (float) (\$args[1] ?? 0.0);\n        if (\$x < 0.0 || \$x > 1.0 || \$y < 0.0 || \$y > 1.0) { throw new \\InvalidArgumentException('coordinates must be between 0 and 1'); }\n        \$slot = (\$y < 0.5 ? 3 : 0) + (\$x < 6 / 16 ? 0 : (\$x < 11 / 16 ? 1 : 2));\n        return self::cases()[\$slot];\n    }\n",
        'getNext' => "    public function getNext(mixed ...\$args): mixed { \$cases = self::cases(); \$i = array_search(\$this, \$cases, true); return \$cases[min(count(\$cases) - 1, \$i + 1)] ?? \$this; }\n",
        'getPrevious' => "    public function getPrevious(mixed ...\$args): mixed { \$cases = self::cases(); \$i = array_search(\$this, \$cases, true); return \$cases[max(0, \$i - 1)] ?? \$this; }\n",
        'getScheduledUpdateDelayTicks' => "    public function getScheduledUpdateDelayTicks(mixed ...\$args): mixed { return match(\$this->name) { 'STABLE' => null, 'FULL_TILT' => 100, default => 10 }; }\n",
        'getFacing' => "    public function getFacing(mixed ...\$args): mixed { return array_search(\$this, self::cases(), true); }\n",
        'isFlammable' => "    public function isFlammable(mixed ...\$args): mixed { return \$this->name !== 'CRIMSON' && \$this->name !== 'WARPED'; }\n",
        'getStandardLogSuffix' => "    public function getStandardLogSuffix(mixed ...\$args): mixed { return \$this->name === 'CRIMSON' || \$this->name === 'WARPED' ? 'Stem' : null; }\n",
        'getAllSidedLogSuffix' => "    public function getAllSidedLogSuffix(mixed ...\$args): mixed { return \$this->name === 'CRIMSON' || \$this->name === 'WARPED' ? 'Hyphae' : null; }\n",
        'getTreeType' => "    public function getTreeType(mixed ...\$args): mixed { return strtolower(\$this->name); }\n",
        'getSoundName' => "    public function getSoundName(mixed ...\$args): mixed { return str_replace('_', ' ', strtolower(\$this->name)); }\n",
        'getSoundId' => "    public function getSoundId(mixed ...\$args): mixed { return array_search(\$this, self::cases(), true); }\n",
        'getTranslatableName' => "    public function getTranslatableName(mixed ...\$args): mixed { return \$this->getSoundName(); }\n",
        default => "    public function {$name}(mixed ...\$args): mixed { return null; }\n",
    };
}

function traitMethod(string $name): string
{
    return match ($name) {
        'describeBlockItemState' => "    public function describeBlockItemState(mixed ...\$args): void { \$this->compatMethod(__FUNCTION__, \$args); }\n",
        'place' => "    public function place(mixed ...\$args): bool { return \$this->compatMethod(__FUNCTION__, \$args); }\n",
        'onNearbyBlockChange' => "    public function onNearbyBlockChange(): void { \$this->compatMethod(__FUNCTION__, []); }\n",
        'onProjectileHit' => "    public function onProjectileHit(mixed ...\$args): void { \$this->compatMethod(__FUNCTION__, \$args); }\n",
        'canBeReplaced' => "    public function canBeReplaced(): bool { return \$this->compatMethod(__FUNCTION__, []); }\n",
        'getDropsForIncompatibleTool' => "    public function getDropsForIncompatibleTool(\\pocketmine\\item\\Item \$item): array { return \$this->compatMethod(__FUNCTION__, [\$item]); }\n",
        'getFlameEncouragement', 'getFlammability', 'getLightLevel' => "    public function {$name}(): int { return \$this->compatMethod(__FUNCTION__, []); }\n",
        'isLit', 'isPowered', 'isWaxed', 'isDead', 'hasFace' => "    public function {$name}(mixed ...\$args): mixed { return \$this->compatMethod(__FUNCTION__, \$args); }\n",
        default => "    public function {$name}(mixed ...\$args): mixed { return \$this->compatMethod(__FUNCTION__, \$args); }\n",
    };
}

function classMethod(string $class, string $name, bool $static): string
{
    if ($static) {
        return match ($name) {
            'weighted', 'binomial', 'discrete' => "    public static function {$name}(mixed ...\$args): mixed { return max((int) (\$args[1] ?? 0), min((int) (\$args[2] ?? \$args[1] ?? 0), (int) (\$args[1] ?? 0))); }\n",
            'bonusChanceDivisor', 'bonusChanceFixed' => "    public static function {$name}(mixed ...\$args): mixed { return true; }\n",
            'grow', 'spread', 'form', 'melt', 'die', 'Block' => "    public static function {$name}(mixed ...\$args): mixed { return true; }\n",
            'calculateMultiplier' => "    public static function {$name}(mixed ...\$args): mixed { return 1.0; }\n",
            'hasEnoughLight', 'canGrow' => "    public static function {$name}(mixed ...\$args): mixed { return true; }\n",
            default => "    public static function {$name}(mixed ...\$args): mixed { return null; }\n",
        };
    }
    return match ($name) {
        'getColor' => "    public function getColor(mixed ...\$args): mixed { return \$this->args[1] ?? \$this->args[0] ?? null; }\n",
        'getType' => "    public function getType(mixed ...\$args): mixed { return \$this->args[0] ?? null; }\n",
        'getOptimalFlowDirections' => "    public function getOptimalFlowDirections(mixed ...\$args): mixed { return [2, 3, 4, 5]; }\n",
        default => "    public function {$name}(mixed ...\$args): mixed { return \$this->args[0] ?? null; }\n",
    };
}
