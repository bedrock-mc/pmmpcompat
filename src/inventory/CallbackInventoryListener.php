<?php

declare(strict_types=1);

namespace pocketmine\inventory;

use pocketmine\item\Item;

class CallbackInventoryListener implements InventoryListener
{
    public function __construct(private ?\Closure $onSlotChange = null, private ?\Closure $onContentChange = null) {}

    public static function onAnyChange(\Closure $onChange): self
    {
        return new self(
            static function(Inventory $inventory, int $slot, Item $oldItem) use ($onChange): void {
                $onChange($inventory);
            },
            static function(Inventory $inventory, array $oldContents) use ($onChange): void {
                $onChange($inventory);
            }
        );
    }

    /** @param array<int, Item> $oldContents */
    public function onContentChange(Inventory $inventory, array $oldContents): void
    {
        if ($this->onContentChange !== null) {
            ($this->onContentChange)($inventory, $oldContents);
        }
    }

    public function onSlotChange(Inventory $inventory, int $slot, Item $oldItem): void
    {
        if ($this->onSlotChange !== null) {
            ($this->onSlotChange)($inventory, $slot, $oldItem);
        }
    }
}
