<?php

declare(strict_types=1);

namespace pocketmine\utils;

use pocketmine\thread\Thread;

class ServerKiller extends Thread
{
    private bool $stopped = false;

    public function __construct(public int $time = 15)
    {
    }

    protected function onRun(): void
    {
        // The compatibility layer never force-kills the PHP host process.
    }

    public function quit(): void
    {
        $this->stopped = true;
        parent::quit();
    }

    public function getThreadName(): string
    {
        return 'Server Killer';
    }
}
