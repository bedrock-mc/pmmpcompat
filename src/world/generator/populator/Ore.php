<?php

declare(strict_types=1);

namespace pocketmine\world\generator\populator;

use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\object\Ore as OreObject;
use pocketmine\world\generator\object\OreType;

class Ore implements Populator
{
    /** @var OreType[] */
    private array $oreTypes = [];

    /** @param OreType[] $oreTypes */
    public function setOreTypes(array $oreTypes): void
    {
        $this->oreTypes = array_values($oreTypes);
    }

    public function populate(mixed $world = null, int $chunkX = 0, int $chunkZ = 0, mixed $random = null): void
    {
        if (!$world instanceof ChunkManager) {
            return;
        }
        $random = $random instanceof Random ? $random : new Random((($chunkX * 73428767) ^ ($chunkZ * 912931)) & 0x7fffffff);
        foreach ($this->oreTypes as $type) {
            for ($i = 0; $i < max(1, $type->clusterCount); ++$i) {
                $x = ($chunkX << 4) + $random->nextRange(0, 15);
                $z = ($chunkZ << 4) + $random->nextRange(0, 15);
                $minY = max($world->getMinY(), $type->minHeight);
                $maxY = min($world->getMaxY() - 1, $type->maxHeight);
                if ($maxY < $minY) {
                    continue;
                }
                (new OreObject($random, $type))->placeObject($world, $x, $random->nextRange($minY, $maxY), $z);
            }
        }
    }
}
