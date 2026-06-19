<?php

declare(strict_types=1);

namespace pocketmine\world\format\io;

use pocketmine\item\Item;

class GlobalItemDataHandlers
{
    private static mixed $serializer = null;
    private static mixed $deserializer = null;
    private static mixed $upgrader = null;

    public static function getDeserializer(): mixed
    {
        return self::$deserializer ??= new class {
            public function deserializeType(mixed $data): Item
            {
                if (is_array($data)) {
                    return new Item((string) ($data['name'] ?? $data['type_id'] ?? 'minecraft:air'), (string) ($data['name'] ?? 'Air'), (int) ($data['count'] ?? 1));
                }
                if (is_object($data) && method_exists($data, 'getName')) {
                    return new Item((string) $data->getName(), (string) $data->getName());
                }
                return new Item('minecraft:air', 'Air', 0);
            }
        };
    }
    public static function getSerializer(): mixed
    {
        return self::$serializer ??= new class {
            public function serializeType(Item $item): object
            {
                return new class($item) {
                    public function __construct(private Item $item) {}
                    public function getName(): string { return $this->item->getTypeId(); }
                    public function getMeta(): int { return 0; }
                    public function getBlock(): mixed { return null; }
                    public function getTag(): mixed { return $this->item->getNamedTag(); }
                };
            }
        };
    }
    public static function getUpgrader(): mixed
    {
        return self::$upgrader ??= new class {
            public function upgradeItemTypeData(mixed $data): mixed { return $data; }
        };
    }
}
