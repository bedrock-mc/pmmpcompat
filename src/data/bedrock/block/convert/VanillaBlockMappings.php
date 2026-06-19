<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block\convert;

use pocketmine\block\VanillaBlocks;

class VanillaBlockMappings
{
    public static function init(?BlockStateToObjectDeserializer $deserializer = null, ?BlockObjectToStateSerializer $serializer = null): BlockSerializerDeserializerRegistrar
    {
        $registrar = new BlockSerializerDeserializerRegistrar($deserializer ?? new BlockStateToObjectDeserializer(), $serializer ?? new BlockObjectToStateSerializer());
        $registrar->mapSimple(VanillaBlocks::AIR(), 'minecraft:air');
        $registrar->mapSimple(VanillaBlocks::STONE(), 'minecraft:stone');
        $registrar->mapSimple(VanillaBlocks::DIRT(), 'minecraft:dirt');
        $registrar->mapSimple(VanillaBlocks::GRASS(), 'minecraft:grass');
        return $registrar;
    }
}
