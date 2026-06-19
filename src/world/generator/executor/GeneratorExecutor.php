<?php

declare(strict_types=1);

namespace pocketmine\world\generator\executor;

interface GeneratorExecutor
{
    public function populate(int $chunkX, int $chunkZ, mixed $centerChunk, array $adjacentChunks, \Closure $onCompletion): void;
    public function shutdown(): void;
}
