<?php

declare(strict_types=1);

namespace pocketmine\world\generator;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\world\ChunkManager;
use pocketmine\world\format\Chunk;

final class PopulationUtils
{
    public static function populateChunkWithAdjacents(int $minY, int $maxY, Generator $generator, int $chunkX, int $chunkZ, mixed $centerChunk, array $adjacentChunks): array
    {
        $manager = new class($minY, $maxY) implements ChunkManager {
            /** @var array<string, Chunk> */
            private array $chunks = [];
            /** @var array<string, Block> */
            private array $blocks = [];

            public function __construct(private int $minY, private int $maxY) {}
            public function getMinY(): int { return $this->minY; }
            public function getMaxY(): int { return $this->maxY; }
            public function isInWorld(int $x, int $y, int $z): bool { return $y >= $this->minY && $y < $this->maxY; }
            public function getChunk(int $chunkX, int $chunkZ): ?Chunk { return $this->chunks[$chunkX . ':' . $chunkZ] ?? null; }
            public function setChunk(int $chunkX, int $chunkZ, Chunk $chunk): void { $this->chunks[$chunkX . ':' . $chunkZ] = $chunk; }
            public function getBlockAt(int $x, int $y, int $z): Block { return $this->blocks[$x . ':' . $y . ':' . $z] ?? VanillaBlocks::AIR(); }
            public function setBlockAt(int $x, int $y, int $z, Block $block): void { $this->blocks[$x . ':' . $y . ':' . $z] = $block; }
        };

        $manager->setChunk($chunkX, $chunkZ, $centerChunk instanceof Chunk ? $centerChunk : new Chunk());
        foreach ($adjacentChunks as $hash => $chunk) {
            [$relativeX, $relativeZ] = self::decodeRelativeChunkHash($hash);
            $manager->setChunk($chunkX + $relativeX, $chunkZ + $relativeZ, $chunk instanceof Chunk ? $chunk : new Chunk());
        }

        $generator->generateChunk($manager, $chunkX, $chunkZ);
        $generator->populateChunk($manager, $chunkX, $chunkZ);

        $centerChunk = $manager->getChunk($chunkX, $chunkZ);
        if (is_object($centerChunk) && method_exists($centerChunk, 'setPopulated')) {
            $centerChunk->setPopulated();
        }

        $resultAdjacents = [];
        foreach ($adjacentChunks as $hash => $_) {
            [$relativeX, $relativeZ] = self::decodeRelativeChunkHash($hash);
            $resultAdjacents[$hash] = $manager->getChunk($chunkX + $relativeX, $chunkZ + $relativeZ);
        }
        return [$centerChunk, $resultAdjacents];
    }

    /** @return array{int, int} */
    private static function decodeRelativeChunkHash(int|string $hash): array
    {
        if (is_string($hash) && str_contains($hash, ':')) {
            [$x, $z] = array_map('intval', explode(':', $hash, 2));
            return [$x, $z];
        }
        return [(int) $hash, 0];
    }
}
