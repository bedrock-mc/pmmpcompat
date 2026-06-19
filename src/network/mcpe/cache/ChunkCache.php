<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\cache;

class ChunkCache
{
    /** @var array<int, array<int, self>> */
    private static array $instances = [];
    /** @var array<string, string> */
    private array $caches = [];
    private int $hits = 0;
    private int $misses = 0;

    public static function getInstance(mixed $world, mixed $compressor): self
    {
        $worldId = is_object($world) ? spl_object_id($world) : crc32(serialize($world));
        $compressorId = is_object($compressor) ? spl_object_id($compressor) : crc32(serialize($compressor));
        return self::$instances[$worldId][$compressorId] ??= new self($world, $compressor);
    }

    public static function pruneCaches(): void
    {
        foreach (self::$instances as $compressorMap) {
            foreach ($compressorMap as $cache) {
                $cache->caches = [];
            }
        }
    }

    private function __construct(private mixed $world, private mixed $compressor) {}

    public function request(int $chunkX, int $chunkZ): string
    {
        $key = $chunkX . ':' . $chunkZ;
        if (isset($this->caches[$key])) {
            ++$this->hits;
            return $this->caches[$key];
        }
        ++$this->misses;
        return $this->caches[$key] = '';
    }

    public function onChunkChanged(int $chunkX, int $chunkZ, mixed $chunk = null): void { unset($this->caches[$chunkX . ':' . $chunkZ]); }
    public function onChunkUnloaded(int $chunkX, int $chunkZ, mixed $chunk = null): void { unset($this->caches[$chunkX . ':' . $chunkZ]); }
    public function onBlockChanged(mixed $block): void
    {
        if (is_object($block) && method_exists($block, 'getFloorX') && method_exists($block, 'getFloorZ')) {
            unset($this->caches[($block->getFloorX() >> 4) . ':' . ($block->getFloorZ() >> 4)]);
        } else {
            $this->caches = [];
        }
    }

    public function calculateCacheSize(): int
    {
        $size = 0;
        foreach ($this->caches as $cache) {
            $size += strlen($cache);
        }
        return $size;
    }

    public function getHitPercentage(): float
    {
        $total = $this->hits + $this->misses;
        return $total === 0 ? 0.0 : ($this->hits / $total) * 100.0;
    }
}
