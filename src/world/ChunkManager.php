<?php

declare(strict_types=1);

namespace pocketmine\world;

use pocketmine\block\Block;
use pocketmine\world\format\Chunk;

interface ChunkManager
{
    public function getBlockAt(int $x, int $y, int $z): Block;
    public function setBlockAt(int $x, int $y, int $z, Block $block): void;
    public function getChunk(int $chunkX, int $chunkZ): ?Chunk;
    public function setChunk(int $chunkX, int $chunkZ, Chunk $chunk): void;
    public function getMinY(): int;
    public function getMaxY(): int;
    public function isInWorld(int $x, int $y, int $z): bool;
}
