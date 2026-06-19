<?php

declare(strict_types=1);

namespace pocketmine\world\format\io\region;

use pocketmine\world\format\io\BaseWorldProvider;
use pocketmine\world\format\io\FastChunkSerializer;
use pocketmine\world\format\io\LoadedChunkData;

abstract class RegionWorldProvider extends BaseWorldProvider
{
    /** @var array<string, RegionLoader> */
    protected array $regions = [];
    protected mixed $logger;

    public function __construct(string $path = '', mixed $logger = null)
    {
        parent::__construct($path);
        $this->logger = $logger;
        if ($path !== '' && !is_dir($path . '/region')) {
            @mkdir($path . '/region', 0777, true);
        }
    }

    abstract protected static function getRegionFileExtension(): string;
    abstract protected static function getPcWorldFormatVersion(): int;

    public static function isValid(string $path): bool
    {
        $regionPath = $path . '/region';
        if (!is_file($path . '/level.dat') || !is_dir($regionPath)) {
            return false;
        }
        foreach (scandir($regionPath) ?: [] as $file) {
            if (preg_match('/^r\\.-?\\d+\\.-?\\d+\\.' . preg_quote(static::getRegionFileExtension(), '/') . '$/', $file) === 1) {
                return true;
            }
        }
        return false;
    }

    public static function getRegionIndex(int $chunkX, int $chunkZ, &$regionX, &$regionZ): void
    {
        $regionX = $chunkX >> 5;
        $regionZ = $chunkZ >> 5;
    }

    protected function pathToRegion(int $regionX, int $regionZ): string
    {
        return $this->path . '/region/r.' . $regionX . '.' . $regionZ . '.' . static::getRegionFileExtension();
    }

    protected function loadRegion(int $regionX, int $regionZ, bool $create = false): RegionLoader
    {
        $key = $regionX . ':' . $regionZ;
        if (!isset($this->regions[$key])) {
            $path = $this->pathToRegion($regionX, $regionZ);
            $this->regions[$key] = is_file($path) ? RegionLoader::loadExisting($path) : ($create ? RegionLoader::createNew($path) : throw new \RuntimeException("Region file $path does not exist"));
        }
        return $this->regions[$key];
    }

    protected function unloadRegion(int $regionX, int $regionZ): void
    {
        $key = $regionX . ':' . $regionZ;
        if (isset($this->regions[$key])) {
            $this->regions[$key]->close();
            unset($this->regions[$key]);
        }
    }

    public function close(): void
    {
        foreach ($this->regions as $key => $region) {
            $region->close();
            unset($this->regions[$key]);
        }
    }

    public function doGarbageCollection(): void
    {
        $limit = time() - 300;
        foreach ($this->regions as $key => $region) {
            if ($region->lastUsed <= $limit) {
                $region->close();
                unset($this->regions[$key]);
            }
        }
    }

    public function loadChunk(int $chunkX, int $chunkZ): ?LoadedChunkData
    {
        self::getRegionIndex($chunkX, $chunkZ, $regionX, $regionZ);
        if (!is_file($this->pathToRegion($regionX, $regionZ))) {
            return null;
        }
        $raw = $this->loadRegion($regionX, $regionZ)->readChunk($chunkX & 0x1f, $chunkZ & 0x1f);
        if ($raw === null) {
            return null;
        }
        return $this->deserializeChunk($raw);
    }

    public function getAllChunks(bool $skipCorrupted = false, ?\Logger $logger = null): \Generator
    {
        $regionPath = $this->path . '/region';
        if (!is_dir($regionPath)) {
            return;
        }
        foreach (scandir($regionPath) ?: [] as $file) {
            if (preg_match('/^r\\.(-?\\d+)\\.(-?\\d+)\\.' . preg_quote(static::getRegionFileExtension(), '/') . '$/', $file, $matches) !== 1) {
                continue;
            }
            $regionX = (int) $matches[1];
            $regionZ = (int) $matches[2];
            $loader = $this->loadRegion($regionX, $regionZ);
            for ($x = 0; $x < 32; $x++) {
                for ($z = 0; $z < 32; $z++) {
                    try {
                        $raw = $loader->readChunk($x, $z);
                        if ($raw !== null) {
                            yield [($regionX << 5) + $x, ($regionZ << 5) + $z] => $this->deserializeChunk($raw);
                        }
                    } catch (\Throwable $e) {
                        if (!$skipCorrupted) {
                            throw $e;
                        }
                    }
                }
            }
            $this->unloadRegion($regionX, $regionZ);
        }
    }

    public function calculateChunkCount(): int
    {
        $count = 0;
        $regionPath = $this->path . '/region';
        if (!is_dir($regionPath)) {
            return 0;
        }
        foreach (scandir($regionPath) ?: [] as $file) {
            if (preg_match('/^r\\.(-?\\d+)\\.(-?\\d+)\\.' . preg_quote(static::getRegionFileExtension(), '/') . '$/', $file, $matches) === 1) {
                $count += $this->loadRegion((int) $matches[1], (int) $matches[2])->calculateChunkCount();
                $this->unloadRegion((int) $matches[1], (int) $matches[2]);
            }
        }
        return $count;
    }

    protected function deserializeChunk(string $data): ?LoadedChunkData
    {
        $decoded = FastChunkSerializer::deserializeTerrain($data);
        return $decoded instanceof LoadedChunkData ? $decoded : null;
    }
}
