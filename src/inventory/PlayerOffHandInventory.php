<?php

declare(strict_types=1);

namespace pocketmine\inventory;

final class PlayerOffHandInventory extends SimpleInventory
{
    public function __construct(private ?object $holder = null)
    {
        parent::__construct(1);
    }

    public function getHolder(): ?object { return $this->holder; }
}
