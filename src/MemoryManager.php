<?php

declare(strict_types=1);

namespace pocketmine;

class MemoryManager
{
    private bool $lowMemory = false;
    private int $globalMemoryLimit = 0;
    private int $lowMemChunkRadiusOverride = 4;

    public function __construct(private ?Server $server = null) {}

    public function isLowMemory(): bool
    {
        return $this->lowMemory;
    }

    public function getGlobalMemoryLimit(): int
    {
        return $this->globalMemoryLimit;
    }

    public function canUseChunkCache(): bool
    {
        return !$this->lowMemory;
    }

    public function getViewDistance(int $distance): int
    {
        return ($this->lowMemory && $this->lowMemChunkRadiusOverride > 0) ? min($this->lowMemChunkRadiusOverride, $distance) : $distance;
    }

    public function trigger(int $memory, int $limit, bool $global = false, int $triggerCount = 0): void
    {
        $this->lowMemory = true;
        $this->triggerGarbageCollector();
    }

    public function check(): void
    {
        $this->triggerGarbageCollector();
    }

    public function triggerGarbageCollector(): int
    {
        $cycles = gc_collect_cycles();
        if (function_exists('gc_mem_caches')) {
            gc_mem_caches();
        }
        return $cycles;
    }

    public function dumpServerMemory(string $outputFolder, int $maxNesting, int $maxStringSize): void
    {
        $logger = $this->server?->getLogger() ?? new class {
            public function info(mixed $message): void {}
        };
        MemoryDump::dumpMemory($this->server ?? $this, $outputFolder, $maxNesting, $maxStringSize, $logger);
    }

    public static function dumpMemory(mixed $startingObject, string $outputFolder, int $maxNesting, int $maxStringSize, mixed $logger): void
    {
        MemoryDump::dumpMemory($startingObject, $outputFolder, $maxNesting, $maxStringSize, $logger);
    }
}
