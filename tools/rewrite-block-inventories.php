<?php

declare(strict_types=1);

$dir = __DIR__ . '/../src/block/inventory';
$ref = dirname(__DIR__, 3) . '/refs/pocketmine/PocketMine-MP/src/block/inventory';

$sizes = [
    'AnvilInventory' => 2,
    'BarrelInventory' => 27,
    'BrewingStandInventory' => 5,
    'CampfireInventory' => 4,
    'CartographyTableInventory' => 2,
    'ChestInventory' => 27,
    'CraftingTableInventory' => 9,
    'EnchantInventory' => 2,
    'EnderChestInventory' => 27,
    'FurnaceInventory' => 3,
    'HopperInventory' => 5,
    'LoomInventory' => 3,
    'ShulkerBoxInventory' => 27,
    'SmithingTableInventory' => 3,
    'StonecutterInventory' => 1,
];

foreach (glob($dir . '/*.php') as $file) {
    $code = file_get_contents($file);
    if ($code === false || !str_contains($code, 'Generated PMMP compatibility stub.')) {
        continue;
    }
    preg_match('/\b(class|interface|trait)\s+(\w+)/', $code, $m);
    if ($m === []) {
        continue;
    }
    $type = $m[1];
    $class = $m[2];
    preg_match_all('/^\s*public\s+const\s+[^;]+;/m', (string) @file_get_contents($ref . '/' . $class . '.php'), $constMatches);
    $constants = $constMatches[0] ?: [];
    preg_match_all('/public\s+function\s+(\w+)\s*\([^)]*\)/', $code, $methodMatches);
    $methods = $methodMatches[1] ?? [];

    if ($type === 'interface') {
        $out = headerFor('pocketmine\\block\\inventory') . "interface {$class}\n{\n    public function getHolder(): \\pocketmine\\world\\Position;\n}\n";
    } elseif ($type === 'trait') {
        $out = headerFor('pocketmine\\block\\inventory') . "trait {$class}\n{\n";
        if ($class === 'BlockInventoryTrait') {
            $out .= "    public function getHolder(): \\pocketmine\\world\\Position { return \$this->holder; }\n";
        } else {
            $out .= "    public function getViewerCount(): int { return count(\$this->getViewers()); }\n";
            $out .= "    public function getViewers(): array { return parent::getViewers(); }\n";
            $out .= "    public function onClose(object \$who): void { parent::onClose(\$who); }\n";
            $out .= "    public function onOpen(object \$who): void { parent::onOpen(\$who); }\n";
        }
        $out .= "}\n";
    } else {
        $size = $sizes[$class] ?? 27;
        $out = headerFor('pocketmine\\block\\inventory') . "class {$class} extends CompatBlockInventory\n{\n";
        if ($class === 'DoubleChestInventory') {
            $out .= "    private ?ChestInventory \$left = null;\n    private ?ChestInventory \$right = null;\n\n";
            $out .= "    public function __construct(mixed ...\$args) { \$this->left = \$args[0] instanceof ChestInventory ? \$args[0] : null; \$this->right = \$args[1] instanceof ChestInventory ? \$args[1] : null; parent::__construct(\$this->left?->getHolder(), 54); }\n";
        } else {
            $out .= "    public function __construct(mixed ...\$args) { parent::__construct(\$args[0] ?? null, {$size}); }\n";
        }
        foreach ($constants as $constant) {
            $out .= "    " . trim($constant) . "\n";
        }
        foreach ($methods as $method) {
            if ($method === '__construct') {
                continue;
            }
            $out .= methodBody($class, $method);
        }
        $out .= "}\n";
    }
    file_put_contents($file, $out);
}

function headerFor(string $namespace): string
{
    return "<?php\n\ndeclare(strict_types=1);\n\nnamespace {$namespace};\n\n";
}

function methodBody(string $class, string $method): string
{
    return match ($method) {
        'getFuel' => "    public function getFuel(mixed ...\$args): mixed { return \$this->itemAt(self::SLOT_FUEL); }\n",
        'getResult' => "    public function getResult(mixed ...\$args): mixed { return \$this->itemAt(self::SLOT_RESULT); }\n",
        'getSmelting' => "    public function getSmelting(mixed ...\$args): mixed { return \$this->itemAt(self::SLOT_INPUT); }\n",
        'setFuel' => "    public function setFuel(mixed ...\$args): mixed { return \$this->setItemAt(self::SLOT_FUEL, \$args[0] ?? null); }\n",
        'setResult' => "    public function setResult(mixed ...\$args): mixed { return \$this->setItemAt(self::SLOT_RESULT, \$args[0] ?? null); }\n",
        'setSmelting' => "    public function setSmelting(mixed ...\$args): mixed { return \$this->setItemAt(self::SLOT_INPUT, \$args[0] ?? null); }\n",
        'getInput' => "    public function getInput(mixed ...\$args): mixed { return \$this->itemAt(self::SLOT_INPUT); }\n",
        'getLapis' => "    public function getLapis(mixed ...\$args): mixed { return \$this->itemAt(self::SLOT_LAPIS); }\n",
        'getOption' => "    public function getOption(mixed ...\$args): mixed { return null; }\n",
        'getOutput' => "    public function getOutput(mixed ...\$args): mixed { return null; }\n",
        'getFurnaceType' => "    public function getFurnaceType(mixed ...\$args): mixed { return \$this->type ?? null; }\n",
        'getMaxStackSize' => "    public function getMaxStackSize(): int { return " . ($class === 'CampfireInventory' ? '1' : 'parent::getMaxStackSize()') . "; }\n",
        'canAddItem' => "    public function canAddItem(\\pocketmine\\item\\Item \$item): bool { return parent::canAddItem(\$item); }\n",
        'getEnderInventory' => "    public function getEnderInventory(mixed ...\$args): mixed { return \$this; }\n",
        'getInventory' => "    public function getInventory(mixed ...\$args): mixed { return \$this; }\n",
        'getContents' => "    public function getContents(bool \$includeEmpty = false): array { return parent::getContents(\$includeEmpty); }\n",
        'getItem' => "    public function getItem(int \$index): \\pocketmine\\item\\Item { return parent::getItem(\$index); }\n",
        'getSize' => "    public function getSize(): int { return parent::getSize(); }\n",
        'isSlotEmpty' => "    public function isSlotEmpty(int \$index): bool { return parent::isSlotEmpty(\$index); }\n",
        'getViewerCount' => "    public function getViewerCount(): int { return parent::getViewerCount(); }\n",
        'onClose' => "    public function onClose(object \$who): void { parent::onClose(\$who); }\n",
        'getLeftSide' => "    public function getLeftSide(mixed ...\$args): mixed { return \$this->left ?? null; }\n",
        'getRightSide' => "    public function getRightSide(mixed ...\$args): mixed { return \$this->right ?? null; }\n",
        default => "    public function {$method}(mixed ...\$args): mixed { return null; }\n",
    };
}
