<?php

declare(strict_types=1);

namespace pocketmine\inventory\transaction;

use pocketmine\inventory\Inventory;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;

final class TransactionBuilderInventory
{
    /** @var array<int, Item> */
    private array $changedSlots = [];

    public function __construct(private Inventory $actualInventory) {}

    public function getActualInventory(): Inventory
    {
        return $this->actualInventory;
    }

    public function getSize(): int
    {
        return $this->actualInventory->getSize();
    }

    public function getItem(int $index): Item
    {
        return isset($this->changedSlots[$index]) ? clone $this->changedSlots[$index] : $this->actualInventory->getItem($index);
    }

    /** @return array<int, Item> */
    public function getContents(bool $includeEmpty = false): array
    {
        $contents = $this->actualInventory->getContents($includeEmpty);
        foreach ($this->changedSlots as $index => $item) {
            if ($includeEmpty || !$item->isNull()) {
                $contents[$index] = clone $item;
            } else {
                unset($contents[$index]);
            }
        }
        return $contents;
    }

    public function setItem(int $index, Item $item): void
    {
        if (!$this->actualInventory->slotExists($index)) {
            return;
        }
        $this->changedSlots[$index] = $item->isNull() ? VanillaItems::AIR() : clone $item;
    }

    public function clear(int $index): void
    {
        $this->setItem($index, VanillaItems::AIR());
    }

    /** @param array<int, Item> $items */
    public function setContents(array $items): void
    {
        for ($slot = 0; $slot < $this->getSize(); ++$slot) {
            $this->setItem($slot, $items[$slot] ?? VanillaItems::AIR());
        }
    }

    /** @return SlotChangeAction[] */
    public function generateActions(): array
    {
        $actions = [];
        foreach ($this->changedSlots as $index => $newItem) {
            $oldItem = $this->actualInventory->getItem($index);
            if (!$newItem->equalsExact($oldItem)) {
                $actions[] = new SlotChangeAction($this->actualInventory, $index, $oldItem, $newItem);
            }
        }
        return $actions;
    }
}
