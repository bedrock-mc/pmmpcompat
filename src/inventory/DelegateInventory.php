<?php

declare(strict_types=1);

namespace pocketmine\inventory;

use pocketmine\item\Item;

class DelegateInventory extends BaseInventory
{
    public function __construct(private Inventory $backingInventory)
    {
        parent::__construct($backingInventory->getSize());
    }

    public function __destruct() {}
    /** @return array<int, Item> */
    public function getContents(bool $includeEmpty = false): array { return $this->backingInventory->getContents($includeEmpty); }
    public function getItem(int $index): Item { return $this->backingInventory->getItem($index); }
    public function getSize(): int { return $this->backingInventory->getSize(); }
    public function isSlotEmpty(int $index): bool { return $this->backingInventory->isSlotEmpty($index); }
    public function setItem(int $index, Item $item, bool $syncHost = true): void { $this->backingInventory->setItem($index, $item, $syncHost); }
    /** @param array<int, Item> $items */
    public function setContents(array $items): void { $this->backingInventory->setContents($items); }
}
