<?php

declare(strict_types=1);

spl_autoload_register(static function(string $class): void {
    $prefix = 'pocketmine\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }

    $path = dirname(__DIR__) . '/src/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (is_file($path)) {
        require $path;
    }
});

use pocketmine\data\bedrock\block\BlockStateData;
use pocketmine\data\bedrock\item\BlockItemIdMap;
use pocketmine\data\bedrock\item\ItemDeserializer;
use pocketmine\data\bedrock\item\ItemSerializer;
use pocketmine\data\bedrock\item\ItemSerializerDeserializerRegistrar;
use pocketmine\data\bedrock\item\ItemTypeDeserializeException;
use pocketmine\data\bedrock\item\ItemTypeNames;
use pocketmine\data\bedrock\item\ItemTypeSerializeException;
use pocketmine\data\bedrock\item\SavedItemData;
use pocketmine\data\bedrock\item\SavedItemStackData;
use pocketmine\data\bedrock\item\UnsupportedItemTypeException;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\enchantment\EnchantingOption;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\StringTag;

function assertTrue(bool $condition, string $message): void
{
    if (!$condition) {
        fwrite(STDERR, "item-data-smoke failed: {$message}\n");
        exit(1);
    }
}

$extraTag = CompoundTag::create()->setString('custom', 'value');
$block = BlockStateData::current('minecraft:stone', ['stone_type' => 'smooth']);
$item = new SavedItemData('minecraft:stone', 2, $block, $extraTag);
$stack = new SavedItemStackData($item, 64, 5, true, ['minecraft:dirt'], ['minecraft:oak_log']);
$nbt = $stack->toNbt();

assertTrue($item->getName() === 'minecraft:stone', 'SavedItemData name getter');
assertTrue($item->getMeta() === 2, 'SavedItemData meta getter');
assertTrue($item->getBlock() === $block, 'SavedItemData block getter');
assertTrue($item->getTag() === $extraTag, 'SavedItemData tag getter');
assertTrue($stack->getTypeData() === $item, 'SavedItemStackData type getter');
assertTrue($stack->getCount() === 64, 'SavedItemStackData count getter');
assertTrue($stack->getSlot() === 5, 'SavedItemStackData slot getter');
assertTrue($stack->getWasPickedUp() === true, 'SavedItemStackData pickup getter');
assertTrue($stack->getCanPlaceOn() === ['minecraft:dirt'], 'SavedItemStackData canPlaceOn getter');
assertTrue($stack->getCanDestroy() === ['minecraft:oak_log'], 'SavedItemStackData canDestroy getter');
assertTrue($nbt->getString(SavedItemData::TAG_NAME) === 'minecraft:stone', 'NBT item name');
assertTrue($nbt->getTag(SavedItemData::TAG_DAMAGE) instanceof ShortTag, 'NBT damage tag');
assertTrue($nbt->getTag(SavedItemStackData::TAG_COUNT) instanceof ByteTag, 'NBT count tag');
assertTrue($nbt->getTag(SavedItemStackData::TAG_CAN_PLACE_ON) instanceof ListTag, 'NBT canPlaceOn tag');
assertTrue($nbt->getTag(SavedItemStackData::TAG_CAN_DESTROY) instanceof ListTag, 'NBT canDestroy tag');
assertTrue($nbt->getTag(SavedItemData::TAG_TAG) === $extraTag, 'NBT custom tag');

$blockTag = $nbt->getCompoundTag(SavedItemData::TAG_BLOCK);
assertTrue($blockTag instanceof CompoundTag, 'NBT block tag');
assertTrue($blockTag->getString(BlockStateData::TAG_NAME) === 'minecraft:stone', 'NBT block name');
$states = $blockTag->getCompoundTag(BlockStateData::TAG_STATES);
assertTrue($states instanceof CompoundTag, 'NBT block states');
assertTrue($states->getTag('stone_type') instanceof StringTag, 'NBT block state value');

$enchantment = new Enchantment('smoke', Rarity::COMMON, ItemFlags::ALL, 0, 1);
$instance = new EnchantmentInstance($enchantment, 1);
$option = new EnchantingOption(3, 'smoke words', [$instance]);
assertTrue($option->getRequiredXpLevel() === 3, 'EnchantingOption xp getter');
assertTrue($option->getDisplayName() === 'smoke words', 'EnchantingOption display getter');
assertTrue($option->getEnchantments() === [$instance], 'EnchantingOption enchantments getter');

assertTrue(is_subclass_of(ItemTypeDeserializeException::class, RuntimeException::class), 'deserialize exception base');
assertTrue(is_subclass_of(ItemTypeSerializeException::class, LogicException::class), 'serialize exception base');
assertTrue(is_subclass_of(UnsupportedItemTypeException::class, ItemTypeDeserializeException::class), 'unsupported exception base');

assertTrue(ItemTypeNames::DIAMOND_SWORD === 'minecraft:diamond_sword', 'item type constants expose Bedrock string IDs');

$idMap = BlockItemIdMap::getInstance();
assertTrue($idMap->lookupItemId('minecraft:stone') === 'minecraft:stone', 'block item map resolves stone block to item');
assertTrue($idMap->lookupBlockId('minecraft:dirt') === 'minecraft:dirt', 'block item map resolves dirt item to block');

$serializer = new ItemSerializer();
$serializedType = $serializer->serializeType(VanillaItems::DIAMOND());
assertTrue($serializedType->getName() === 'minecraft:diamond', 'item serializer defaults to type ID');
$serializedStack = $serializer->serializeStack(VanillaItems::DIAMOND()->setCount(3), 7);
assertTrue($serializedStack->getCount() === 3 && $serializedStack->getSlot() === 7, 'item serializer preserves count and slot');

$deserializer = new ItemDeserializer();
$deserialized = $deserializer->deserializeStack(new SavedItemStackData(new SavedItemData('minecraft:diamond_sword'), 2, null, null, [], []));
assertTrue($deserialized->getTypeId() === 'minecraft:diamond_sword' && $deserialized->getCount() === 2, 'item deserializer returns local item with count');

$registrar = new ItemSerializerDeserializerRegistrar();
$registrar->map1to1Item(VanillaItems::DIAMOND(), 'minecraft:diamond');
assertTrue($registrar->serializer->serializeType(VanillaItems::DIAMOND())->getName() === 'minecraft:diamond', 'item registrar wires serializer');
assertTrue($registrar->deserializer->deserializeType('minecraft:diamond')->getTypeId() === 'minecraft:diamond', 'item registrar wires deserializer');
$registrar->map1to1Block(VanillaBlocks::STONE(), 'minecraft:stone');
assertTrue($registrar->serializer->serializeType(VanillaItems::STONE())->getName() === 'minecraft:stone', 'item registrar maps block item serializer');

echo "item-data-smoke ok\n";
