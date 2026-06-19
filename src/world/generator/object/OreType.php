<?php

declare(strict_types=1);

namespace pocketmine\world\generator\object;

use pocketmine\block\Block;

class OreType
{
    public function __construct(
        public Block $material,
        public Block $replaces,
        public int $clusterCount,
        public int $clusterSize,
        public int $minHeight,
        public int $maxHeight,
    ) {}
}
