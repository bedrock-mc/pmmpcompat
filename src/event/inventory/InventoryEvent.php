<?php

declare(strict_types=1);

namespace pocketmine\event\inventory;

use pocketmine\event\Event;
use pocketmine\inventory\Inventory;

abstract class InventoryEvent extends Event
{
    public function __construct(protected Inventory $inventory) {}

    public function getInventory(): Inventory
    {
        return $this->inventory;
    }

    public function getViewers(): array
    {
        return $this->inventory->getViewers();
    }
}
