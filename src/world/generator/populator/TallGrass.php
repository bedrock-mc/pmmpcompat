<?php

declare(strict_types=1);

namespace pocketmine\world\generator\populator;

use pocketmine\math\Vector3;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\object\TallGrass as TallGrassObject;

class TallGrass implements Populator
{
    private int $baseAmount = 0;
    private int $randomAmount = 0;

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
        $random = $random instanceof Random ? $random : new Random((($chunkX * 8191) ^ ($chunkZ * 131071)) & 0x7fffffff);
        $count = $this->baseAmount + ($this->randomAmount > 0 ? $random->nextRange(0, $this->randomAmount) : 0);
        if ($count === 0) {
            return;
        }
        TallGrassObject::growGrass($world, new Vector3(($chunkX << 4) + 8, max(0, $world->getMinY()), ($chunkZ << 4) + 8), $random, $count, 8);
    }
}
