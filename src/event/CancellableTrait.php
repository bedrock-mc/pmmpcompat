<?php

declare(strict_types=1);

namespace pocketmine\event;

trait CancellableTrait
{
    private bool $cancelled = false;

    public function isCancelled(): bool
    {
        return $this->cancelled;
    }

    public function setCancelled(bool $cancelled = true): void
    {
        $this->cancelled = $cancelled;
    }

    public function cancel(): void
    {
        $this->setCancelled(true);
    }

    public function uncancel(): void
    {
        $this->setCancelled(false);
    }
}
