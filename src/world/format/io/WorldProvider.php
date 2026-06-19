<?php

declare(strict_types=1);

namespace pocketmine\world\format\io;

interface WorldProvider
{
    public function calculateChunkCount(): int;
    public function close(): void;
    public function doGarbageCollection(): void;
    public function getAllChunks(bool $skipCorrupted = false, ?\Logger $logger = null): \Generator;
    public function getPath(): string;
    public function getWorldData(): WorldData;
    public function getWorldMaxY(): int;
    public function getWorldMinY(): int;
    public function loadChunk(int $chunkX, int $chunkZ): ?LoadedChunkData;
}
