<?php

declare(strict_types=1);

namespace pocketmine\inventory\transaction\action;

use pocketmine\inventory\Inventory;
use pocketmine\inventory\transaction\TransactionValidationException;
use pocketmine\item\Item;
use pocketmine\player\Player;

class SlotChangeAction extends InventoryAction
{
    public function __construct(private Inventory $inventory, private int $slot, Item $sourceItem, Item $targetItem)
    {
        parent::__construct($sourceItem, $targetItem);
    }

    public function getInventory(): Inventory
    {
        return $this->inventory;
    }

    public function getSlot(): int
    {
        return $this->slot;
    }

    public function validate(Player $source): void
    {
        if (!$this->inventory->slotExists($this->slot)) {
            throw new TransactionValidationException('Slot ' . $this->slot . ' does not exist in inventory');
        }
        if (!$this->inventory->getItem($this->slot)->equalsExact($this->sourceItem)) {
            throw new TransactionValidationException('Slot does not contain expected original item');
        }
        if ($this->targetItem->getCount() > $this->targetItem->getMaxStackSize()) {
            throw new TransactionValidationException('Target item exceeds item type max stack size');
        }
        if ($this->targetItem->getCount() > $this->inventory->getMaxStackSize()) {
            throw new TransactionValidationException('Target item exceeds inventory max stack size');
        }
    }

    public function execute(Player $source): void
    {
        $this->inventory->setItem($this->slot, $this->getTargetItem());
    }
}
