<?php

declare(strict_types=1);

require __DIR__ . '/../autoload.php';

use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\block\BlockLegacyMetadata;
use pocketmine\data\bedrock\block\BlockStateData;
use pocketmine\data\bedrock\block\BlockStateNames;
use pocketmine\data\bedrock\block\BlockStateStringValues;
use pocketmine\data\bedrock\block\BlockTypeNames;
use pocketmine\data\bedrock\block\convert\BlockObjectToStateSerializer;
use pocketmine\data\bedrock\block\convert\BlockSerializerDeserializerRegistrar;
use pocketmine\data\bedrock\block\convert\VanillaBlockMappings;
use pocketmine\data\bedrock\block\convert\BlockStateToObjectDeserializer;
use pocketmine\data\bedrock\block\convert\BlockStateWriter;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\IntTag;

function check(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$serializer = new BlockObjectToStateSerializer();
$serializer->mapSimple(VanillaBlocks::AIR(), 'minecraft:air');
$serializer->map(VanillaBlocks::STONE(), BlockStateWriter::create('minecraft:stone')->writeInt('stone_type', 0));

$airState = $serializer->serializeBlock(VanillaBlocks::AIR());
check($airState->getName() === 'minecraft:air', 'air serializes to minecraft:air');
check($airState->getStates() === [], 'air has no state properties');
check($serializer->serialize(VanillaBlocks::AIR()->getStateId())->equals($airState), 'state ID serialization uses registered air block');

$stoneState = $serializer->serializeBlock(VanillaBlocks::STONE());
check($stoneState->getName() === 'minecraft:stone', 'stone serializes to minecraft:stone');
check($stoneState->getState('stone_type') instanceof IntTag, 'stone_type is an IntTag');
check($stoneState->getState('stone_type')?->getValue() === 0, 'stone_type value is preserved');

$deserializer = new BlockStateToObjectDeserializer();
$deserializer->mapSimple('minecraft:air', fn() => VanillaBlocks::AIR());
$deserializer->map('minecraft:stone', function($in) {
    check($in->readInt('stone_type') === 0, 'stone_type is read during deserialization');
    return VanillaBlocks::STONE();
});
$deserializer->map('minecraft:test_toggle', function($in) {
    check($in->readBool('powered') === true, 'boolean state is read during deserialization');
    return VanillaBlocks::DIRT();
});

check($deserializer->deserializeBlock($airState)->getTypeId() === 'minecraft:air', 'air deserializes to VanillaBlocks::AIR');
check($deserializer->deserialize($airState) === VanillaBlocks::AIR()->getStateId(), 'air deserializes to its state ID');
check($deserializer->deserializeBlock($stoneState)->getTypeId() === 'minecraft:stone', 'stone deserializes to VanillaBlocks::STONE');
check($deserializer->deserialize(BlockStateData::current('minecraft:test_toggle', ['powered' => new ByteTag(1)])) === VanillaBlocks::DIRT()->getStateId(), 'property-bearing state deserializes to a runtime state ID');

check(BlockTypeNames::AIR === 'minecraft:air', 'block type constants expose Bedrock string IDs');
check(BlockStateNames::PILLAR_AXIS === 'pillar_axis', 'block state name constants expose Bedrock state keys');
check(BlockStateStringValues::PILLAR_AXIS_Y === 'pillar_axis_y', 'block state value constants expose string values');
check(BlockLegacyMetadata::LIQUID_FALLING_FLAG === 0x08, 'legacy metadata constants expose useful bit flags');

$registrar = VanillaBlockMappings::init();
check($registrar instanceof BlockSerializerDeserializerRegistrar, 'VanillaBlockMappings::init returns a registrar');
check($registrar->serializer->serializeBlock(VanillaBlocks::DIRT())->getName() === 'minecraft:dirt', 'vanilla mappings register dirt serializer');
check($registrar->deserializer->deserializeBlock(BlockStateData::current('minecraft:grass'))->getTypeId() === 'minecraft:grass', 'vanilla mappings register grass deserializer');

echo "block-data smoke ok\n";
