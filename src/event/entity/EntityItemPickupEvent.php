<?php

declare(strict_types=1);

namespace pocketmine\event\entity;

use pocketmine\entity\Entity;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;

class EntityItemPickupEvent extends EntityEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(
        Entity $collector,
        private Entity $origin,
        private Item $item,
        private ?Inventory $inventory,
    ) {
        $this->entity = $collector;
    }

    public function getOrigin(): Entity
    {
        return $this->origin;
    }

    public function getItem(): Item
    {
        return clone $this->item;
    }

    public function setItem(Item $item): void
    {
        $this->item = clone $item;
    }

    public function getInventory(): ?Inventory
    {
        return $this->inventory;
    }

    public function setInventory(?Inventory $inventory): void
    {
        $this->inventory = $inventory;
    }
}
