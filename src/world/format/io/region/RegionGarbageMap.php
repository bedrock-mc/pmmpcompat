<?php

declare(strict_types=1);

namespace pocketmine\world\format\io\region;

class RegionGarbageMap
{
    /** @var array<int, RegionLocationTableEntry> */
    private array $entries = [];
    private bool $clean = false;

    public function __construct(array $entries)
    {
        foreach ($entries as $entry) {
            $this->entries[$entry->getFirstSector()] = $entry;
        }
    }

    public static function buildFromLocationTable(array $locationTable): self
    {
        $used = [];
        foreach ($locationTable as $entry) {
            if ($entry === null) {
                continue;
            }
            if (isset($used[$entry->getFirstSector()])) {
                throw new \RuntimeException('Overlapping entries detected');
            }
            $used[$entry->getFirstSector()] = $entry;
        }
        ksort($used, SORT_NUMERIC);
        $garbage = [];
        $previous = null;
        foreach ($used as $entry) {
            $expectedStart = $previous !== null ? $previous->getLastSector() + 1 : RegionLoader::FIRST_SECTOR;
            if ($expectedStart < $entry->getFirstSector()) {
                $garbage[$expectedStart] = new RegionLocationTableEntry($expectedStart, $entry->getFirstSector() - $expectedStart, 0);
            } elseif ($expectedStart > $entry->getFirstSector()) {
                throw new \RuntimeException('Overlapping entries detected');
            }
            $previous = $entry;
        }
        return new self($garbage);
    }

    public function getArray(): array
    {
        if (!$this->clean) {
            ksort($this->entries, SORT_NUMERIC);
            $merged = [];
            foreach ($this->entries as $entry) {
                $lastKey = array_key_last($merged);
                if ($lastKey !== null && $merged[$lastKey]->getLastSector() + 1 === $entry->getFirstSector()) {
                    $merged[$lastKey] = new RegionLocationTableEntry($merged[$lastKey]->getFirstSector(), $merged[$lastKey]->getSectorCount() + $entry->getSectorCount(), 0);
                } else {
                    $merged[$entry->getFirstSector()] = $entry;
                }
            }
            $this->entries = $merged;
            $this->clean = true;
        }
        return $this->entries;
    }

    public function add(RegionLocationTableEntry $entry): void
    {
        foreach ($this->entries as $existing) {
            if ($entry->overlaps($existing)) {
                throw new \InvalidArgumentException('Overlapping entry starting at ' . $entry->getFirstSector());
            }
        }
        $this->entries[$entry->getFirstSector()] = $entry;
        $this->clean = false;
    }

    public function remove(RegionLocationTableEntry $entry): void
    {
        unset($this->entries[$entry->getFirstSector()]);
    }

    public function end(): ?RegionLocationTableEntry
    {
        $entries = $this->getArray();
        $end = end($entries);
        return $end !== false ? $end : null;
    }

    public function allocate(int $newSize): ?RegionLocationTableEntry
    {
        foreach ($this->getArray() as $candidate) {
            if ($candidate->getSectorCount() < $newSize) {
                continue;
            }
            $location = new RegionLocationTableEntry($candidate->getFirstSector(), $newSize, time());
            $this->remove($candidate);
            if ($candidate->getSectorCount() > $newSize) {
                $this->add(new RegionLocationTableEntry($candidate->getFirstSector() + $newSize, $candidate->getSectorCount() - $newSize, 0));
            }
            return $location;
        }
        return null;
    }
}
