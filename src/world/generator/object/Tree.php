<?php

declare(strict_types=1);

namespace pocketmine\world\generator\object;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\utils\Random;
use pocketmine\world\BlockTransaction;
use pocketmine\world\ChunkManager;

class Tree
{
    public function __construct(
        protected Block $trunkBlock,
        protected Block $leafBlock,
        protected int $treeHeight = 7,
    ) {}

    public function canPlaceObject(ChunkManager $world, int $x, int $y, int $z, Random $random): bool
    {
        return $world->isInWorld($x, $y, $z) && $world->isInWorld($x, $y + $this->treeHeight, $z);
    }

    public function getBlockTransaction(ChunkManager $world, int $x, int $y, int $z, Random $random): ?BlockTransaction
    {
        if (!$this->canPlaceObject($world, $x, $y, $z, $random)) {
            return null;
        }
        $transaction = new BlockTransaction($world);
        $transaction->addBlockAt($x, $y - 1, $z, VanillaBlocks::DIRT());
        for ($yy = 0; $yy < $this->treeHeight; ++$yy) {
            $transaction->addBlockAt($x, $y + $yy, $z, $this->trunkBlock);
        }
        $top = $y + $this->treeHeight;
        for ($xx = -1; $xx <= 1; ++$xx) {
            for ($zz = -1; $zz <= 1; ++$zz) {
                $transaction->addBlockAt($x + $xx, $top, $z + $zz, $this->leafBlock);
            }
        }
        return $transaction;
    }
}
