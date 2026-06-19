<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$src = $root . '/src/data/bedrock';
$ref = dirname($root, 2) . '/refs/pocketmine/PocketMine-MP/src/data/bedrock';

$constantOnly = [
    'BedrockDataFiles',
    'BiomeIds',
    'CompoundTypeIds',
    'EffectIds',
    'EnchantmentIds',
    'FireworkRocketTypeIds',
    'GoatHornTypeIds',
    'MedicineTypeIds',
    'PotionTypeIds',
    'SuspiciousStewTypeIds',
    'WorldDataVersions',
];

foreach ($constantOnly as $class) {
    $constants = constantsFromReference($ref . '/' . $class . '.php', $class);
    if ($class === 'BedrockDataFiles') {
        $constants = array_map(static fn(string $value): string => preg_replace('/BEDROCK_DATA_PATH/', "__DIR__ . '/../../../resources/bedrock'", $value) ?? $value, $constants);
    }
    if ($class === 'WorldDataVersions') {
        $constants['BLOCK_STATES'] = '(1 << 24) | (21 << 16) | (60 << 8) | 33';
        $constants['CHUNK'] = '0';
        $constants['SUBCHUNK'] = '0';
        $constants['LAST_OPENED_IN'] = '[1, 21, 100, 23, 0]';
    }
    writeClass($src . '/' . $class . '.php', 'pocketmine\\data\\bedrock', $class, $constants, '');
}

$mapClasses = [
    'BannerPatternTypeIdMap' => '\\pocketmine\\block\\utils\\BannerPatternType',
    'FireworkRocketTypeIdMap' => '\\pocketmine\\item\\FireworkRocketType',
    'GoatHornTypeIdMap' => '\\pocketmine\\item\\GoatHornType',
    'MedicineTypeIdMap' => '\\pocketmine\\item\\MedicineType',
    'MobHeadTypeIdMap' => '\\pocketmine\\block\\utils\\MobHeadType',
    'MushroomBlockTypeIdMap' => '\\pocketmine\\block\\utils\\MushroomBlockType',
    'NoteInstrumentIdMap' => '\\pocketmine\\world\\sound\\NoteInstrument',
    'PotionTypeIdMap' => '\\pocketmine\\item\\PotionType',
    'SuspiciousStewTypeIdMap' => '\\pocketmine\\item\\SuspiciousStewType',
];
foreach ($mapClasses as $class => $enumClass) {
    $methods = $class === 'BannerPatternTypeIdMap'
        ? "    public function fromId(mixed ...\$args): mixed { return \$this->compatFromId(\$args[0] ?? null); }\n    public function register(mixed ...\$args): mixed { return \$this->compatRegister(\$args[0] ?? null, \$args[1] ?? null); }\n    public function toId(mixed ...\$args): mixed { return \$this->compatToId(\$args[0] ?? null); }\n"
        : '';
    writeClass($src . '/' . $class . '.php', 'pocketmine\\data\\bedrock', $class, [], "    use \\pocketmine\\utils\\SingletonTrait;\n    use CompatIdMapTrait;\n\n    public function __construct(mixed ...\$args) { \$this->seedEnumCases({$enumClass}::class); }\n" . $methods);
}

writeClass($src . '/EffectIdMap.php', 'pocketmine\\data\\bedrock', 'EffectIdMap', [], "    use \\pocketmine\\utils\\SingletonTrait;\n    use CompatIdMapTrait;\n\n    public function __construct(mixed ...\$args) {}\n");
writeClass($src . '/EnchantmentIdMap.php', 'pocketmine\\data\\bedrock', 'EnchantmentIdMap', [], "    use \\pocketmine\\utils\\SingletonTrait;\n    use CompatIdMapTrait;\n\n    public function __construct(mixed ...\$args) {}\n");

writeClass($src . '/DyeColorIdMap.php', 'pocketmine\\data\\bedrock', 'DyeColorIdMap', [], <<<'PHP'
    use \pocketmine\utils\SingletonTrait;

    public function __construct(mixed ...$args) {}
    public function fromInvertedId(mixed ...$args): mixed { return $this->fromOrdinal(15 - (int) ($args[0] ?? 0)); }
    public function fromItemId(mixed ...$args): mixed { return $this->fromOrdinal((int) ($args[0] ?? 0)); }
    public function toInvertedId(mixed ...$args): mixed { return 15 - $this->ordinal($args[0] ?? null); }
    public function toItemId(mixed ...$args): mixed { return $this->ordinal($args[0] ?? null); }
    private function fromOrdinal(int $id): mixed { $cases = \pocketmine\block\utils\DyeColor::cases(); return $cases[$id] ?? null; }
    private function ordinal(mixed $color): int { return is_object($color) ? max(0, array_search($color, \pocketmine\block\utils\DyeColor::cases(), true) ?: 0) : 0; }
PHP);

writeClass($src . '/ItemTagToIdMap.php', 'pocketmine\\data\\bedrock', 'ItemTagToIdMap', [], <<<'PHP'
    use \pocketmine\utils\SingletonTrait;

    /** @var array<string, array<string, true>> */
    private array $tags = [];
    public function __construct(mixed ...$args) { foreach (($args[0] ?? []) as $tag => $ids) { foreach ((array) $ids as $id) { $this->addIdToTag((string) $tag, (string) $id); } } }
    public function addIdToTag(mixed ...$args): mixed { $this->tags[(string) ($args[0] ?? '')][(string) ($args[1] ?? '')] = true; return null; }
    public function getIdsForTag(mixed ...$args): mixed { return array_keys($this->tags[(string) ($args[0] ?? '')] ?? []); }
    public function tagContainsId(mixed ...$args): mixed { return isset($this->tags[(string) ($args[0] ?? '')][(string) ($args[1] ?? '')]); }
PHP);

writeClass($src . '/LegacyToStringIdMap.php', 'pocketmine\\data\\bedrock', 'LegacyToStringIdMap', [], <<<'PHP'
    /** @var array<int, string> */
    private array $legacyToString = [];
    public function __construct(mixed ...$args) { if (isset($args[0]) && is_array($args[0])) { foreach ($args[0] as $legacy => $string) { $this->add((string) $string, (int) $legacy); } } }
    public function add(mixed ...$args): mixed { $this->legacyToString[(int) ($args[1] ?? 0)] = (string) ($args[0] ?? ''); return null; }
    public function getLegacyToStringMap(mixed ...$args): mixed { return $this->legacyToString; }
    public function legacyToString(mixed ...$args): mixed { return $this->legacyToString[(int) ($args[0] ?? 0)] ?? null; }
PHP, abstract: true);

writeClass($src . '/LegacyBiomeIdToStringIdMap.php', 'pocketmine\\data\\bedrock', 'LegacyBiomeIdToStringIdMap', [], "    use \\pocketmine\\utils\\SingletonTrait;\n\n    public function __construct(mixed ...\$args) { parent::__construct([\\pocketmine\\data\\bedrock\\BiomeIds::PLAINS => 'minecraft:plains']); }\n", extends: 'LegacyToStringIdMap');
writeClass($src . '/LegacyEntityIdToStringIdMap.php', 'pocketmine\\data\\bedrock', 'LegacyEntityIdToStringIdMap', [], "    use \\pocketmine\\utils\\SingletonTrait;\n\n    public function __construct(mixed ...\$args) { parent::__construct([1 => 'minecraft:player']); }\n", extends: 'LegacyToStringIdMap');

file_put_contents($src . '/IntSaveIdMapTrait.php', <<<'PHP'
<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock;

trait IntSaveIdMapTrait
{
    use CompatIdMapTrait;

    public function register(mixed ...$args): mixed { return $this->compatRegister($args[0] ?? null, $args[1] ?? null); }
    public function fromId(mixed ...$args): mixed { return $this->compatFromId($args[0] ?? null); }
    public function toId(mixed ...$args): mixed { return $this->compatToId($args[0] ?? null); }
}
PHP);

function constantsFromReference(string $file, string $class): array
{
    $code = (string) file_get_contents($file);
    preg_match_all('/public\s+const\s+(\w+)\s*=\s*(.*?);/s', $code, $matches, PREG_SET_ORDER);
    $constants = [];
    foreach ($matches as $match) {
        $value = trim(preg_replace('/\s+/', ' ', $match[2]) ?? $match[2]);
        $constants[$match[1]] = $value;
    }
    return $constants;
}

function writeClass(string $file, string $namespace, string $class, array $constants, string $body, bool $abstract = false, ?string $extends = null): void
{
    $out = "<?php\n\n";
    $out .= "declare(strict_types=1);\n\n";
    $out .= "namespace {$namespace};\n\n";
    $out .= ($abstract ? 'abstract ' : '') . "class {$class}" . ($extends !== null ? " extends {$extends}" : '') . "\n{\n";
    foreach ($constants as $name => $value) {
        $out .= "    public const {$name} = {$value};\n";
    }
    if ($constants !== [] && $body !== '') {
        $out .= "\n";
    }
    $out .= $body;
    $out .= "}\n";
    file_put_contents($file, $out);
}
