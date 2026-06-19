<?php

declare(strict_types=1);

namespace pocketmine\item;

final class VanillaItems
{
    public static function AIR(): Item { return new Item('minecraft:air', 'Air', 0); }
    public static function STONE(): Item { return new Item('minecraft:stone', 'Stone'); }
    public static function DIRT(): Item { return new Item('minecraft:dirt', 'Dirt'); }
    public static function DIAMOND(): Item { return new Item('minecraft:diamond', 'Diamond'); }
    public static function DIAMOND_SWORD(): Item { return new Item('minecraft:diamond_sword', 'Diamond Sword'); }

    /** @return array<string, Item> */
    public static function getAll(): array
    {
        return [
            'air' => self::AIR(),
            'stone' => self::STONE(),
            'dirt' => self::DIRT(),
            'diamond' => self::DIAMOND(),
            'diamond_sword' => self::DIAMOND_SWORD(),
        ];
    }
}
