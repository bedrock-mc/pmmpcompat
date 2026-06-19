<?php

declare(strict_types=1);

namespace pocketmine\world\generator\populator;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\world\ChunkManager;

class GroundCover implements Populator
{
    /** @param Block[] $cover */
    public function __construct(private array $cover = []) {}

    /** @param Block[] $cover */
    public function setCover(array $cover): void
    {
        $this->cover = array_values($cover);
    }

    public function populate(mixed $world = null, int $chunkX = 0, int $chunkZ = 0, mixed $random = null): void
    {
        if (!$world instanceof ChunkManager || $this->cover === []) {
            return;
        }
        for ($x = 0; $x < 16; ++$x) {
            for ($z = 0; $z < 16; ++$z) {
                $worldX = ($chunkX << 4) + $x;
                $worldZ = ($chunkZ << 4) + $z;
                for ($y = $world->getMaxY() - 1; $y >= $world->getMinY(); --$y) {
                    if (!$world->getBlockAt($worldX, $y, $worldZ)->hasSameTypeId(VanillaBlocks::AIR())) {
                        foreach ($this->cover as $offset => $block) {
                            $coverY = $y + $offset + 1;
                            if ($world->isInWorld($worldX, $coverY, $worldZ)) {
                                $world->setBlockAt($worldX, $coverY, $worldZ, $block);
                            }
                        }
                        break;
                    }
                }
            }
        }
    }
}
