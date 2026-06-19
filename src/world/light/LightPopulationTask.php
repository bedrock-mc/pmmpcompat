<?php

declare(strict_types=1);

namespace pocketmine\world\light;

use pocketmine\world\format\Chunk;
use pocketmine\world\format\LightArray;

class LightPopulationTask
{
    /** @var array<int, LightArray> */
    private array $resultSkyLightArrays = [];
    /** @var array<int, LightArray> */
    private array $resultBlockLightArrays = [];
    /** @var int[] */
    private array $resultHeightMap;

    /**
     * @param callable(array<int, LightArray>, array<int, LightArray>, int[]): void|null $onCompletion
     */
    public function __construct(private Chunk $chunk, private mixed $onCompletion = null)
    {
        $this->resultHeightMap = $chunk->getHeightMapArray();
    }

    public function onRun(): void
    {
        foreach ($this->chunk->getSubChunks() as $y => $subChunk) {
            $this->resultSkyLightArrays[$y] = $subChunk->getBlockSkyLightArray();
            $this->resultBlockLightArrays[$y] = $subChunk->getBlockLightArray();
        }
        $this->resultHeightMap = $this->chunk->getHeightMapArray();
    }

    public function onCompletion(): void
    {
        if (is_callable($this->onCompletion)) {
            ($this->onCompletion)($this->resultBlockLightArrays, $this->resultSkyLightArrays, $this->resultHeightMap);
        }
    }
}
