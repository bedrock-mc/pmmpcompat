<?php

declare(strict_types=1);

namespace pocketmine\world\biome;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;

class SnowyBiome extends Biome
{
    public function __construct()
    {
        $this->setGroundCover([new Block('minecraft:snow_layer', 'Snow Layer'), VanillaBlocks::GRASS(), VanillaBlocks::DIRT(), VanillaBlocks::DIRT(), VanillaBlocks::DIRT()]);
    }
}
