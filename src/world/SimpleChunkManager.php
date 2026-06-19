<?php

declare(strict_types=1);

namespace pocketmine\world;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\world\format\Chunk;

class SimpleChunkManager implements ChunkManager
{
    /** @var array<string, Chunk> */
    protected array $chunks = [];
    /** @var array<string, Block> */
    private array $blocks = [];

    public function __construct(
        private int $minY,
        private int $maxY,
    ) {}

    public function getBlockAt(int $x, int $y, int $z): Block
    {
        if (!$this->isInWorld($x, $y, $z)) {
            return VanillaBlocks::AIR();
        }
        return $this->blocks[World::blockHash($x, $y, $z)] ?? VanillaBlocks::AIR();
    }

    public function setBlockAt(int $x, int $y, int $z, Block $block): void
    {
        if (!$this->isInWorld($x, $y, $z)) {
            throw new \InvalidArgumentException("Cannot set block at coordinates x=$x,y=$y,z=$z, coordinates are out of bounds");
        }
        $this->blocks[World::blockHash($x, $y, $z)] = clone $block;
    }

    public function getChunk(int $chunkX, int $chunkZ): ?Chunk
    {
        return $this->chunks[World::chunkHash($chunkX, $chunkZ)] ?? null;
    }

    public function setChunk(int $chunkX, int $chunkZ, Chunk $chunk): void
    {
        $this->chunks[World::chunkHash($chunkX, $chunkZ)] = $chunk;
    }

    public function cleanChunks(): void
    {
        $this->chunks = [];
        $this->blocks = [];
    }

    public function getMinY(): int { return $this->minY; }
    public function getMaxY(): int { return $this->maxY; }

    public function isInWorld(int $x, int $y, int $z): bool
    {
        return $x >= -2147483648 && $x <= 2147483647 &&
            $y >= $this->minY && $y < $this->maxY &&
            $z >= -2147483648 && $z <= 2147483647;
    }
}
