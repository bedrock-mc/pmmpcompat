<?php

declare(strict_types=1);

namespace pocketmine\world\generator\populator;

use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\object\TreeFactory;
use pocketmine\world\generator\object\TreeType;

class Tree implements Populator
{
    private int $baseAmount = 0;
    private int $randomAmount = 0;

    public function __construct(private ?TreeType $type = null) {}

    public function setBaseAmount(int $amount): void
    {
        $this->baseAmount = max(0, $amount);
    }

    public function setRandomAmount(int $amount): void
    {
        $this->randomAmount = max(0, $amount);
    }

    public function populate(mixed $world = null, int $chunkX = 0, int $chunkZ = 0, mixed $random = null): void
    {
        if (!$world instanceof ChunkManager) {
            return;
        }
        $random = $random instanceof Random ? $random : new Random((($chunkX * 1439) ^ ($chunkZ * 1709)) & 0x7fffffff);
        $count = $this->baseAmount + ($this->randomAmount > 0 ? $random->nextRange(0, $this->randomAmount) : 0);
        for ($i = 0; $i < $count; ++$i) {
            $tree = TreeFactory::get($random, $this->type);
            if ($tree === null) {
                continue;
            }
            $x = ($chunkX << 4) + $random->nextRange(0, 15);
            $z = ($chunkZ << 4) + $random->nextRange(0, 15);
            $y = max(1, $world->getMinY() + 1);
            $transaction = $tree->getBlockTransaction($world, $x, $y, $z, $random);
            $transaction?->apply();
        }
    }
}
