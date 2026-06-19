<?php

declare(strict_types=1);

namespace pocketmine\inventory;

use pocketmine\item\Item;
use pocketmine\utils\ObjectSet;

class BaseInventory extends Inventory implements SlotValidatedInventory
{
    private ObjectSet $slotValidators;

    public function __construct(int $size = 36)
    {
        parent::__construct($size);
        $this->slotValidators = new ObjectSet();
    }

    /** @return Item[] */
    public function addItem(Item ...$items): array { return parent::addItem(...$items); }
    /** @return array<int, Item> */
    public function all(Item $item): array { return parent::all($item); }
    public function canAddItem(Item $item): bool { return parent::canAddItem($item); }
    public function clear(int $index): void { parent::clear($index); }
    public function clearAll(): void { parent::clearAll(); }
    public function contains(Item $item): bool { return parent::contains($item); }
    public function first(Item $item, bool $exact = false): int { return parent::first($item, $exact); }
    public function firstEmpty(): int { return parent::firstEmpty(); }
    public function getAddableItemQuantity(Item $item): int { return parent::getAddableItemQuantity($item); }
    /** @return object[] */
    public function getListeners(): array { return parent::getListeners(); }
    public function getMaxStackSize(): int { return parent::getMaxStackSize(); }
    public function getSlotValidators(): ObjectSet { return $this->slotValidators; }
    /** @return object[] */
    public function getViewers(): array { return parent::getViewers(); }
    public function isSlotEmpty(int $index): bool { return parent::isSlotEmpty($index); }
    public function onClose(object $who): void { parent::onClose($who); }
    public function onOpen(object $who): void { parent::onOpen($who); }
    public function remove(Item $item): void { parent::remove($item); }
    public function removeAllViewers(bool $send = true): void
    {
        foreach ($this->getViewers() as $viewer) {
            $this->onClose($viewer);
        }
    }
    /** @return Item[] */
    public function removeItem(Item ...$items): array { return parent::removeItem(...$items); }
    /** @param array<int, Item> $items */
    public function setContents(array $items): void
    {
        $oldContents = $this->getContents(true);
        parent::setContents($items);
        $this->notifyContentChange($oldContents);
    }
    public function setItem(int $index, Item $item, bool $syncHost = true): void
    {
        $oldItem = $this->getItem($index);
        parent::setItem($index, $item, $syncHost);
        $this->notifySlotChange($index, $oldItem);
    }
    public function setMaxStackSize(int $size): void { parent::setMaxStackSize($size); }
    public function slotExists(int $slot): bool { return parent::slotExists($slot); }
    public function swap(int $slot1, int $slot2): void { parent::swap($slot1, $slot2); }

    private function notifySlotChange(int $slot, Item $oldItem): void
    {
        foreach ($this->getListeners() as $listener) {
            if ($listener instanceof InventoryListener) {
                $listener->onSlotChange($this, $slot, $oldItem);
            }
        }
    }

    /** @param array<int, Item> $oldContents */
    private function notifyContentChange(array $oldContents): void
    {
        foreach ($this->getListeners() as $listener) {
            if ($listener instanceof InventoryListener) {
                $listener->onContentChange($this, $oldContents);
            }
        }
    }
}
