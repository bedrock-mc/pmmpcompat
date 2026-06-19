<?php

declare(strict_types=1);

namespace pocketmine\inventory;

use pocketmine\crafting\CraftingGrid;

final class PlayerCraftingInventory extends CraftingGrid implements TemporaryInventory
{
    public function __construct(private ?object $holder = null)
    {
        parent::__construct(2);
    }

    public function getHolder(): ?object { return $this->holder; }
}
