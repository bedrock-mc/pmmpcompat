<?php

declare(strict_types=1);

namespace pocketmine;

final class GarbageCollectorManager
{
    private int $threshold = 10001;
    private int $collectionTimeTotalNs = 0;

    public function __construct(mixed ...$args)
    {
        gc_disable();
    }

    public function getThreshold(): int
    {
        return $this->threshold;
    }

    public function getCollectionTimeTotalNs(): int
    {
        return $this->collectionTimeTotalNs;
    }

    public function maybeCollectCycles(): int
    {
        $roots = gc_status()['roots'] ?? 0;
        if ($roots < $this->threshold) {
            return 0;
        }

        $start = hrtime(true);
        $cycles = gc_collect_cycles();
        $this->collectionTimeTotalNs += hrtime(true) - $start;
        return $cycles;
    }
}
