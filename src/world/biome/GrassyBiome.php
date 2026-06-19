<?php

declare(strict_types=1);

namespace pocketmine\world\biome;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;

class GrassyBiome extends Biome
{
    public function __construct()
    {
        $this->setGroundCover([VanillaBlocks::GRASS(), VanillaBlocks::DIRT(), VanillaBlocks::DIRT(), VanillaBlocks::DIRT(), VanillaBlocks::DIRT()]);
    }
}
