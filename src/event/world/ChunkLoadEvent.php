<?php

declare(strict_types=1);

namespace pocketmine\event\world;

use pocketmine\world\format\Chunk;
use pocketmine\world\World;

class ChunkLoadEvent extends ChunkEvent
{
    public function __construct(World $world, int $chunkX, int $chunkZ, Chunk $chunk, private bool $newChunk)
    {
        parent::__construct($world, $chunkX, $chunkZ, $chunk);
    }

    public function isNewChunk(): bool { return $this->newChunk; }
}
