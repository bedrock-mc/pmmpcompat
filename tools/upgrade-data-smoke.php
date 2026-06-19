<?php

declare(strict_types=1);

require dirname(__DIR__) . '/autoload.php';

use pocketmine\data\bedrock\block\BlockStateData;
use pocketmine\data\bedrock\block\upgrade\BlockDataUpgrader;
use pocketmine\data\bedrock\block\upgrade\BlockIdMetaUpgrader;
use pocketmine\data\bedrock\block\upgrade\BlockStateUpgradeSchemaUtils;
use pocketmine\data\bedrock\block\upgrade\BlockStateUpgrader;
use pocketmine\data\bedrock\item\SavedItemData;
use pocketmine\data\bedrock\item\SavedItemStackData;
use pocketmine\data\bedrock\item\upgrade\ItemDataUpgrader;
use pocketmine\data\bedrock\item\upgrade\ItemIdMetaUpgradeSchemaUtils;
use pocketmine\data\bedrock\item\upgrade\ItemIdMetaUpgrader;
use pocketmine\data\bedrock\item\upgrade\LegacyItemIdToStringIdMap;
use pocketmine\data\bedrock\item\upgrade\R12ItemIdToBlockIdMap;
use pocketmine\nbt\tag\CompoundTag;

function assertSame(mixed $expected, mixed $actual, string $message): void
{
    if ($expected !== $actual) {
        fwrite(STDERR, $message . ': expected ' . var_export($expected, true) . ', got ' . var_export($actual, true) . PHP_EOL);
        exit(1);
    }
}

$blockSchema = BlockStateUpgradeSchemaUtils::loadSchemaFromString(json_encode([
    'maxVersionMajor' => 1,
    'maxVersionMinor' => 20,
    'maxVersionPatch' => 0,
    'maxVersionRevision' => 0,
    'renamedIds' => [
        'minecraft:old_stone' => 'minecraft:stone',
    ],
    'addedProperties' => [
        'minecraft:old_stone' => [
            'stone_type' => ['string' => 'smooth'],
        ],
    ],
], JSON_THROW_ON_ERROR), 1);

$blockIdMeta = new BlockIdMetaUpgrader([
    1 => 'minecraft:old_stone',
], [
    'minecraft:old_stone' => [
        0 => new BlockStateData('minecraft:old_stone', [], 0),
    ],
]);
$blockData = new BlockDataUpgrader($blockIdMeta, new BlockStateUpgrader([$blockSchema]));
$block = $blockData->upgradeIntIdMeta(1, 0);
assertSame('minecraft:stone', $block->getName(), 'legacy block ID upgrades through schema rename');
assertSame('smooth', $block->getState('stone_type')?->getValue(), 'block schema added property is preserved');

$itemSchema = ItemIdMetaUpgradeSchemaUtils::loadSchemaFromString(json_encode([
    'renamedIds' => [
        'minecraft:old_apple' => 'minecraft:apple',
    ],
    'remappedMetas' => [
        'minecraft:planks' => [
            2 => 'minecraft:birch_planks',
        ],
    ],
], JSON_THROW_ON_ERROR), 1);
$itemUpgrader = new ItemIdMetaUpgrader([$itemSchema]);
$legacyItems = new LegacyItemIdToStringIdMap([
    260 => 'minecraft:old_apple',
    5 => 'minecraft:planks',
]);
$r12Blocks = new R12ItemIdToBlockIdMap([
    'minecraft:planks' => 'minecraft:old_stone',
]);
$itemData = new ItemDataUpgrader($itemUpgrader, $legacyItems, $r12Blocks, $blockData);

$apple = $itemData->upgradeItemTypeDataInt(260, 0, 3, null);
assertSame('minecraft:apple', $apple->getTypeData()->getName(), 'legacy item ID upgrades through schema rename');
assertSame(3, $apple->getCount(), 'item count preserved');

$planks = $itemData->upgradeItemTypeDataInt(5, 2, 7, null);
assertSame('minecraft:birch_planks', $planks->getTypeData()->getName(), 'item meta remap upgrades to flattened ID');
assertSame('minecraft:stone', $planks->getTypeData()->getBlock()?->getName(), 'r12 block item carries upgraded block state');

$nbtStack = CompoundTag::create()
    ->setString(SavedItemData::TAG_NAME, 'minecraft:old_apple')
    ->setShort(SavedItemData::TAG_DAMAGE, 0)
    ->setByte(SavedItemStackData::TAG_COUNT, 4)
    ->setByte(SavedItemStackData::TAG_SLOT, 9);
$fromNbt = $itemData->upgradeItemStackNbt($nbtStack);
assertSame('minecraft:apple', $fromNbt?->getTypeData()->getName(), 'NBT item stack name upgrades');
assertSame(4, $fromNbt?->getCount(), 'NBT item stack count preserved');
assertSame(9, $fromNbt?->getSlot(), 'NBT item stack slot preserved');

echo "upgrade-data-smoke OK\n";
