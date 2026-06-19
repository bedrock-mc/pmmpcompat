<?php

declare(strict_types=1);

namespace pocketmine\world\generator\object;

use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;

class Ore
{
    public function __construct(
        private Random $random,
        public OreType $type,
    ) {}

    public function getType(): OreType { return $this->type; }

    public function canPlaceObject(ChunkManager $world, int $x, int $y, int $z): bool
    {
        return $world->getBlockAt($x, $y, $z)->hasSameTypeId($this->type->replaces);
    }

    public function placeObject(ChunkManager $world, int $x, int $y, int $z): void
    {
        $clusterSize = max(1, $this->type->clusterSize);
        for ($i = 0; $i < $clusterSize; ++$i) {
            $xx = $x + $this->random->nextRange(-1, 1);
            $yy = $y + $this->random->nextRange(-1, 1);
            $zz = $z + $this->random->nextRange(-1, 1);
            if ($world->isInWorld($xx, $yy, $zz) && $this->canPlaceObject($world, $xx, $yy, $zz)) {
                $world->setBlockAt($xx, $yy, $zz, $this->type->material);
            }
        }
    }
}
