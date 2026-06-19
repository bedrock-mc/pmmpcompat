<?php

declare(strict_types=1);

namespace pocketmine\event;

interface Cancellable
{
    public function isCancelled(): bool;
    public function setCancelled(bool $cancelled = true): void;
}
