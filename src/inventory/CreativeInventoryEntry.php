<?php

declare(strict_types=1);

namespace pocketmine\inventory;

use pocketmine\item\Item;

class CreativeInventoryEntry
{
    private Item $item;

    public function __construct(Item $item, private CreativeCategory $category, private ?CreativeGroup $group = null)
    {
        $this->item = clone $item;
    }

    public function getCategory(): CreativeCategory { return $this->category; }
    public function getGroup(): ?CreativeGroup { return $this->group; }
    public function getItem(): Item { return clone $this->item; }
    public function matchesItem(Item $item): bool { return $item->equals($this->item, true, false); }
}
