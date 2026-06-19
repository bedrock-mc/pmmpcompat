<?php

declare(strict_types=1);

namespace pocketmine\world\generator;

use pocketmine\world\format\Chunk;

class PopulationTask
{
    private mixed $resultChunk = null;
    /** @var array<int|string, mixed> */
    private array $resultAdjacents = [];

    /**
     * @param array<int|string, mixed> $adjacentChunks
     * @param callable(mixed, array<int|string, mixed>): void|null $onCompletion
     */
    public function __construct(
        private int $minY = -64,
        private int $maxY = 320,
        private ?Generator $generator = null,
        private int $chunkX = 0,
        private int $chunkZ = 0,
        private mixed $chunk = null,
        private array $adjacentChunks = [],
        private mixed $onCompletion = null,
    ) {}

    public function onRun(): void
    {
        if ($this->generator === null) {
            $this->resultChunk = $this->chunk instanceof Chunk ? $this->chunk : new Chunk();
            $this->resultAdjacents = $this->adjacentChunks;
            return;
        }

        [$this->resultChunk, $this->resultAdjacents] = PopulationUtils::populateChunkWithAdjacents(
            $this->minY,
            $this->maxY,
            $this->generator,
            $this->chunkX,
            $this->chunkZ,
            $this->chunk instanceof Chunk ? $this->chunk : new Chunk(),
            $this->adjacentChunks,
        );
    }

    public function onCompletion(): void
    {
        if (is_callable($this->onCompletion)) {
            ($this->onCompletion)($this->resultChunk, $this->resultAdjacents);
        }
    }
}
