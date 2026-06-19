<?php

declare(strict_types=1);

namespace pocketmine\world\biome;

use pocketmine\block\Block;

class OceanBiome extends Biome
{
    public function __construct()
    {
        $this->setGroundCover([
            new Block('minecraft:gravel', 'Gravel'),
            new Block('minecraft:gravel', 'Gravel'),
            new Block('minecraft:gravel', 'Gravel'),
            new Block('minecraft:gravel', 'Gravel'),
            new Block('minecraft:gravel', 'Gravel'),
        ]);
        $this->setElevation(46, 58);
        $this->temperature = 0.5;
        $this->rainfall = 0.5;
    }

    public function getName(): string { return 'Ocean'; }
}
