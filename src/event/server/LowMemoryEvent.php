<?php

declare(strict_types=1);

namespace pocketmine\event\server;

use pocketmine\utils\Process;

class LowMemoryEvent extends ServerEvent
{
    public function __construct(
        private int $memory,
        private int $memoryLimit,
        private bool $isGlobal = false,
        private int $triggerCount = 0,
    ) {}

    public function getMemory(): int { return $this->memory; }
    public function getMemoryLimit(): int { return $this->memoryLimit; }
    public function getTriggerCount(): int { return $this->triggerCount; }
    public function isGlobal(): bool { return $this->isGlobal; }
    public function getMemoryFreed(): int
    {
        $usage = Process::getAdvancedMemoryUsage();
        return $this->memory - ($this->isGlobal ? ($usage[1] ?? 0) : ($usage[0] ?? 0));
    }
}
