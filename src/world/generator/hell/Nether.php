<?php

declare(strict_types=1);

namespace pocketmine\world\generator\hell;

use pocketmine\world\ChunkManager;
use pocketmine\world\generator\Generator;

class Nether extends Generator
{
    public function __construct(int $seed, string $preset)
    {
        parent::__construct($seed, $preset);
    }

    public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void {}
    public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void {}
}
