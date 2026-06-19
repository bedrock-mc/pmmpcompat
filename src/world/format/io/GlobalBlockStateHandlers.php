<?php

declare(strict_types=1);

namespace pocketmine\world\format\io;

use pocketmine\data\bedrock\block\BlockStateData;
use pocketmine\data\bedrock\block\convert\BlockObjectToStateSerializer;
use pocketmine\data\bedrock\block\convert\BlockSerializerDeserializerRegistrar;
use pocketmine\data\bedrock\block\convert\BlockStateToObjectDeserializer;

class GlobalBlockStateHandlers
{
    private static ?BlockSerializerDeserializerRegistrar $registrar = null;
    private static mixed $upgrader = null;
    private static ?BlockStateData $unknownBlockStateData = null;

    public static function getDeserializer(): BlockStateToObjectDeserializer { return self::getRegistrar()->deserializer; }
    public static function getRegistrar(): BlockSerializerDeserializerRegistrar
    {
        return self::$registrar ??= new BlockSerializerDeserializerRegistrar(new BlockStateToObjectDeserializer(), new BlockObjectToStateSerializer());
    }
    public static function getSerializer(): BlockObjectToStateSerializer { return self::getRegistrar()->serializer; }
    public static function getUnknownBlockStateData(): BlockStateData { return self::$unknownBlockStateData ??= BlockStateData::current('minecraft:info_update', []); }
    public static function getUpgrader(): mixed
    {
        return self::$upgrader ??= new class {
            public function upgradeIntIdMeta(int $id, int $meta): BlockStateData { return BlockStateData::current('minecraft:unknown', ['legacy_id' => $id, 'legacy_meta' => $meta]); }
            public function upgradeBlockStateNbt(BlockStateData $data): BlockStateData { return $data; }
        };
    }
}
