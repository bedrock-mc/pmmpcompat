<?php

declare(strict_types=1);

namespace pocketmine\world\format\io\region;

use pocketmine\world\format\io\ChunkData;
use pocketmine\world\format\io\FastChunkSerializer;
use pocketmine\world\format\io\LoadedChunkData;
use pocketmine\world\format\io\WritableWorldProvider;

abstract class WritableRegionWorldProvider extends RegionWorldProvider implements WritableWorldProvider
{
    public static function generate(string $path, string $name, mixed $options = null): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        if (!is_dir($path . '/region')) {
            mkdir($path . '/region', 0777, true);
        }
        if (!is_file($path . '/level.dat')) {
            file_put_contents($path . '/level.dat', json_encode(['name' => $name, 'format' => static::getPcWorldFormatVersion()], JSON_PRETTY_PRINT));
        }
    }

    public function saveChunk(int $chunkX, int $chunkZ, ChunkData $chunkData, int $dirtyFlags): void
    {
        self::getRegionIndex($chunkX, $chunkZ, $regionX, $regionZ);
        $this->loadRegion($regionX, $regionZ, true)->writeChunk($chunkX & 0x1f, $chunkZ & 0x1f, $this->serializeChunk($chunkData));
    }

    protected function serializeChunk(ChunkData $chunk): string
    {
        return FastChunkSerializer::serializeTerrain(new LoadedChunkData($chunk, false, LoadedChunkData::FIXER_FLAG_NONE));
    }
}
