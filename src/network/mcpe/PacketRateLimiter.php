<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe;

use pocketmine\network\PacketHandlingException;
use function hrtime;
use function intdiv;
use function min;

final class PacketRateLimiter
{
    private int $budget;
    private int $lastUpdateTimeNs;
    private int $maxBudget;

    public function __construct(
        private string $name,
        private int $averagePerTick,
        int $maxBufferTicks,
        private int $updateFrequencyNs = 50_000_000,
    ) {
        $this->maxBudget = $this->averagePerTick * $maxBufferTicks;
        $this->budget = $this->maxBudget;
        $this->lastUpdateTimeNs = hrtime(true);
    }

    public function decrement(int $amount = 1): void
    {
        if ($this->budget <= 0) {
            $this->update();
            if ($this->budget <= 0) {
                throw new PacketHandlingException("Exceeded rate limit for \"$this->name\"");
            }
        }
        $this->budget -= $amount;
    }

    public function update(): void
    {
        $nowNs = hrtime(true);
        $timeSinceLastUpdateNs = $nowNs - $this->lastUpdateTimeNs;
        if ($timeSinceLastUpdateNs > $this->updateFrequencyNs) {
            $ticksSinceLastUpdate = intdiv($timeSinceLastUpdateNs, $this->updateFrequencyNs);
            $this->budget = min($this->budget, $this->maxBudget) + ($this->averagePerTick * 2 * $ticksSinceLastUpdate);
            $this->lastUpdateTimeNs = $nowNs;
        }
    }
}
