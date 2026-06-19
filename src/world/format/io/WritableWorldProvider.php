<?php

declare(strict_types=1);

namespace pocketmine\world\format\io;

interface WritableWorldProvider extends WorldProvider
{
    public function saveChunk(int $chunkX, int $chunkZ, ChunkData $chunkData, int $dirtyFlags): void;
}
