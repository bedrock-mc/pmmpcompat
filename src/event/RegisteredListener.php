<?php

declare(strict_types=1);

namespace pocketmine\event;

use pocketmine\plugin\Plugin;
use pocketmine\timings\TimingsHandler;

class RegisteredListener
{
    public function __construct(
        private \Closure $handler,
        private int $priority,
        private Plugin $plugin,
        private bool $handleCancelled,
        private TimingsHandler $timings
    ) {
        if (!in_array($priority, EventPriority::ALL, true)) {
            throw new \InvalidArgumentException('Invalid priority number ' . $priority);
        }
    }

    public function getHandler(): \Closure
    {
        return $this->handler;
    }

    public function getPlugin(): Plugin
    {
        return $this->plugin;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function callEvent(Event $event): void
    {
        if ($event instanceof Cancellable && $event->isCancelled() && !$this->handleCancelled) {
            return;
        }
        $this->timings->startTiming();
        try {
            ($this->handler)($event);
        } finally {
            $this->timings->stopTiming();
        }
    }

    public function isHandlingCancelled(): bool
    {
        return $this->handleCancelled;
    }
}
