<?php

declare(strict_types=1);

namespace pocketmine\world\generator\object;

use pocketmine\block\Block;
use pocketmine\utils\Random;
use pocketmine\world\BlockTransaction;
use pocketmine\world\ChunkManager;

class OakTree extends Tree
{
    public function __construct()
    {
        parent::__construct(new Block('minecraft:oak_log', 'Oak Log'), new Block('minecraft:oak_leaves', 'Oak Leaves'));
    }

    public function getBlockTransaction(ChunkManager $world, int $x, int $y, int $z, Random $random): ?BlockTransaction
    {
        $this->treeHeight = $random->nextBoundedInt(3) + 4;
        return parent::getBlockTransaction($world, $x, $y, $z, $random);
    }
}
