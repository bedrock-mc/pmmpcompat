<?php

declare(strict_types=1);

namespace pocketmine\world\format\io\region;

class RegionLocationTableEntry
{
    public function __construct(
        private int $firstSector,
        private int $sectorCount,
        private int $timestamp,
    ) {
        if ($firstSector < 0 || $firstSector >= 2 ** 24) {
            throw new \InvalidArgumentException("Start sector must be positive, got $firstSector");
        }
        if ($sectorCount < 1) {
            throw new \InvalidArgumentException("Sector count must be positive, got $sectorCount");
        }
    }

    public function getFirstSector(): int
    {
        return $this->firstSector;
    }

    public function getLastSector(): int
    {
        return $this->firstSector + $this->sectorCount - 1;
    }

    public function getUsedSectors(): array
    {
        return range($this->getFirstSector(), $this->getLastSector());
    }

    public function getSectorCount(): int
    {
        return $this->sectorCount;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function overlaps(RegionLocationTableEntry $other): bool
    {
        return $this->getFirstSector() <= $other->getLastSector() && $other->getFirstSector() <= $this->getLastSector();
    }
}
