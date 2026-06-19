<?php

declare(strict_types=1);

namespace pocketmine\world\generator\object;

use pocketmine\block\Block;
use pocketmine\utils\Random;
use pocketmine\world\BlockTransaction;
use pocketmine\world\ChunkManager;

class BirchTree extends Tree
{
    public function __construct(protected bool $superBirch = false)
    {
        parent::__construct(new Block('minecraft:birch_log', 'Birch Log'), new Block('minecraft:birch_leaves', 'Birch Leaves'));
    }

    public function getBlockTransaction(ChunkManager $world, int $x, int $y, int $z, Random $random): ?BlockTransaction
    {
        $this->treeHeight = $random->nextBoundedInt(3) + 5 + ($this->superBirch ? 5 : 0);
        return parent::getBlockTransaction($world, $x, $y, $z, $random);
    }
}
