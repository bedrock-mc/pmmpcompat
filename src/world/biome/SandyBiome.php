<?php

declare(strict_types=1);

namespace pocketmine\world\biome;

use pocketmine\block\Block;

class SandyBiome extends Biome
{
    public function __construct()
    {
        $this->setGroundCover([
            new Block('minecraft:sand', 'Sand'),
            new Block('minecraft:sand', 'Sand'),
            new Block('minecraft:sandstone', 'Sandstone'),
            new Block('minecraft:sandstone', 'Sandstone'),
            new Block('minecraft:sandstone', 'Sandstone'),
        ]);
    }
}
