<?php

declare(strict_types=1);

namespace pocketmine\world\generator;

use pocketmine\world\ChunkManager;

class Flat extends Generator
{
    public function __construct(int $seed, string $preset)
    {
        parent::__construct($seed, $preset !== '' ? $preset : '2;bedrock,2xdirt,grass;1;');
    }

    public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void {}
    public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void {}
}
