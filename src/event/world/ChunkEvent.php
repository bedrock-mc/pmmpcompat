<?php

declare(strict_types=1);

namespace pocketmine\event\world;

use pocketmine\world\format\Chunk;
use pocketmine\world\World;

abstract class ChunkEvent extends WorldEvent
{
    public function __construct(World $world, private int $chunkX, private int $chunkZ, private Chunk $chunk)
    {
        parent::__construct($world);
    }

    public function getChunk(): Chunk { return $this->chunk; }
    public function getChunkX(): int { return $this->chunkX; }
    public function getChunkZ(): int { return $this->chunkZ; }
}
