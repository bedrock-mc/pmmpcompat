<?php

declare(strict_types=1);

namespace pocketmine\inventory;

use pocketmine\item\Item;

interface InventoryListener
{
    /** @param array<int, Item> $oldContents */
    public function onContentChange(Inventory $inventory, array $oldContents): void;
    public function onSlotChange(Inventory $inventory, int $slot, Item $oldItem): void;
}
