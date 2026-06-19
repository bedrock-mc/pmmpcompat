<?php

declare(strict_types=1);

namespace pocketmine\inventory;

final class PlayerEnderInventory extends SimpleInventory
{
    public function __construct(private ?object $holder = null, int $size = 27)
    {
        parent::__construct($size);
    }

    public function getHolder(): ?object { return $this->holder; }
}
