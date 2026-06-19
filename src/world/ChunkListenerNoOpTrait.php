<?php

declare(strict_types=1);

namespace pocketmine\world;

use pocketmine\math\Vector3;
use pocketmine\world\format\Chunk;

/**
 * Default no-op implementations for ChunkListener methods.
 */
trait ChunkListenerNoOpTrait
{
    public function onChunkChanged(int $chunkX, int $chunkZ, Chunk $chunk): void {}
    public function onChunkLoaded(int $chunkX, int $chunkZ, Chunk $chunk): void {}
    public function onChunkUnloaded(int $chunkX, int $chunkZ, Chunk $chunk): void {}
    public function onChunkPopulated(int $chunkX, int $chunkZ, Chunk $chunk): void {}
    public function onBlockChanged(Vector3 $block): void {}
}
