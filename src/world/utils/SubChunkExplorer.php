<?php

declare(strict_types=1);

namespace pocketmine\world\utils;

use pocketmine\world\ChunkManager;
use pocketmine\world\format\SubChunk;

class SubChunkExplorer
{
    private ?SubChunk $currentSubChunk = null;
    private int $currentChunkX = 0;
    private int $currentChunkY = 0;
    private int $currentChunkZ = 0;

    public function __construct(private ?ChunkManager $world = null) {}

    public function invalidate(): void
    {
        $this->currentSubChunk = null;
    }

    public function isValid(): bool
    {
        return $this->currentSubChunk !== null;
    }

    public function moveTo(int $x, int $y, int $z): int
    {
        return $this->moveToChunk($x >> 4, $y >> 4, $z >> 4);
    }

    public function moveToChunk(int $chunkX, int $chunkY, int $chunkZ): int
    {
        if ($this->world === null) {
            $this->invalidate();
            return SubChunkExplorerStatus::INVALID;
        }

        $chunk = $this->world->getChunk($chunkX, $chunkZ);
        if ($chunk === null) {
            $this->invalidate();
            return SubChunkExplorerStatus::INVALID;
        }

        try {
            $subChunk = $chunk->getSubChunk($chunkY);
        } catch (\InvalidArgumentException) {
            $this->invalidate();
            return SubChunkExplorerStatus::INVALID;
        }

        $moved = !$this->isValid() || $this->currentChunkX !== $chunkX || $this->currentChunkY !== $chunkY || $this->currentChunkZ !== $chunkZ;
        $this->currentChunkX = $chunkX;
        $this->currentChunkY = $chunkY;
        $this->currentChunkZ = $chunkZ;
        $this->currentSubChunk = $subChunk;
        return $moved ? SubChunkExplorerStatus::MOVED : SubChunkExplorerStatus::OK;
    }

    public function currentSubChunk(): ?SubChunk
    {
        return $this->currentSubChunk;
    }
}
