<?php

declare(strict_types=1);

namespace pocketmine\inventory;

use pocketmine\item\Item;
use pocketmine\utils\ObjectSet;

class PlayerInventory extends SimpleInventory
{
    private int $heldItemIndex = 0;
    private ObjectSet $heldItemIndexChangeListeners;

    public function __construct(private ?object $holder = null)
    {
        $this->heldItemIndexChangeListeners = new ObjectSet();
        parent::__construct(36);
    }

    public function getHeldItemIndex(): int { return $this->heldItemIndex; }
    public function getHeldItemIndexChangeListeners(): ObjectSet { return $this->heldItemIndexChangeListeners; }
    public function getHolder(): ?object { return $this->holder; }
    public function getHotbarSize(): int { return 9; }
    public function getHotbarSlotItem(int $hotbarSlot): Item
    {
        $this->throwIfNotHotbarSlot($hotbarSlot);
        return $this->getItem($hotbarSlot);
    }
    public function getItemInHand(): Item { return $this->getHotbarSlotItem($this->heldItemIndex); }
    public function isHotbarSlot(int $slot): bool { return $slot >= 0 && $slot < $this->getHotbarSize(); }
    public function setHeldItemIndex(int $hotbarSlot): void
    {
        $this->throwIfNotHotbarSlot($hotbarSlot);
        $oldIndex = $this->heldItemIndex;
        $this->heldItemIndex = $hotbarSlot;
        foreach ($this->heldItemIndexChangeListeners as $listener) {
            if ($listener instanceof \Closure) {
                $listener($oldIndex);
            }
        }
    }
    public function setItemInHand(Item $item): void { $this->setItem($this->heldItemIndex, $item); }

    private function throwIfNotHotbarSlot(int $slot): void
    {
        if (!$this->isHotbarSlot($slot)) {
            throw new \InvalidArgumentException($slot . ' is not a valid hotbar slot index');
        }
    }
}
