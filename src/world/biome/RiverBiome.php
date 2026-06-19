<?php

declare(strict_types=1);

namespace pocketmine\world\biome;

use pocketmine\block\VanillaBlocks;

class RiverBiome extends Biome
{
    public function __construct()
    {
        $this->setGroundCover([VanillaBlocks::DIRT(), VanillaBlocks::DIRT(), VanillaBlocks::DIRT(), VanillaBlocks::DIRT(), VanillaBlocks::DIRT()]);
        $this->setElevation(58, 62);
        $this->temperature = 0.5;
        $this->rainfall = 0.7;
    }

    public function getName(): string { return 'River'; }
}
