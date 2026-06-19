<?php

declare(strict_types=1);

namespace pocketmine\network;

use function array_fill;
use function array_sum;
use function count;
use function max;

final class BandwidthStatsTracker
{
    /** @var int[] */
    private array $history;
    private int $nextHistoryIndex = 0;
    private int $bytesSinceLastRotation = 0;
    private int $totalBytes = 0;

    public function __construct(int $historySize)
    {
        $this->history = array_fill(0, max(1, $historySize), 0);
    }

    public function add(int $bytes): void
    {
        $this->totalBytes += $bytes;
        $this->bytesSinceLastRotation += $bytes;
    }

    public function getTotalBytes(): int
    {
        return $this->totalBytes;
    }

    public function rotateHistory(): void
    {
        $this->history[$this->nextHistoryIndex] = $this->bytesSinceLastRotation;
        $this->bytesSinceLastRotation = 0;
        $this->nextHistoryIndex = ($this->nextHistoryIndex + 1) % count($this->history);
    }

    public function getAverageBytes(): float
    {
        return array_sum($this->history) / count($this->history);
    }

    public function resetHistory(): void
    {
        $this->history = array_fill(0, count($this->history), 0);
        $this->nextHistoryIndex = 0;
        $this->bytesSinceLastRotation = 0;
    }
}
