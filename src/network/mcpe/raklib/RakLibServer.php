<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\raklib;

class RakLibServer
{
    private bool $running = false;

    public function __construct(mixed ...$args) {}
    public function getThreadName(): string { return 'RakLib'; }
    public function startAndWait(mixed ...$args): void { $this->running = true; }
    public function isRunning(): bool { return $this->running; }
    public function quit(): void { $this->running = false; }
    public function getCrashInfo(): mixed { return null; }
}
