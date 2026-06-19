<?php

declare(strict_types=1);

namespace pocketmine\block;

final class VanillaBlocks
{
    public static function AIR(): Block { return new Block('minecraft:air', 'Air'); }
    public static function STONE(): Block { return new Block('minecraft:stone', 'Stone'); }
    public static function DIRT(): Block { return new Block('minecraft:dirt', 'Dirt'); }
    public static function GRASS(): Block { return new Block('minecraft:grass', 'Grass'); }

    /** @return array<string, Block> */
    public static function getAll(): array
    {
        return [
            'air' => self::AIR(),
            'stone' => self::STONE(),
            'dirt' => self::DIRT(),
            'grass' => self::GRASS(),
        ];
    }
}
