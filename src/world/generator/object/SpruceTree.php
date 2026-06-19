<?php

declare(strict_types=1);

namespace pocketmine\world\generator\object;

use pocketmine\block\Block;
use pocketmine\utils\Random;
use pocketmine\world\BlockTransaction;
use pocketmine\world\ChunkManager;

class SpruceTree extends Tree
{
    public function __construct()
    {
        parent::__construct(new Block('minecraft:spruce_log', 'Spruce Log'), new Block('minecraft:spruce_leaves', 'Spruce Leaves'), 10);
    }

    public function getBlockTransaction(ChunkManager $world, int $x, int $y, int $z, Random $random): ?BlockTransaction
    {
        $this->treeHeight = $random->nextBoundedInt(4) + 6;
        return parent::getBlockTransaction($world, $x, $y, $z, $random);
    }
}
