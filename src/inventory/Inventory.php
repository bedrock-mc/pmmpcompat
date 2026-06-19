<?php

declare(strict_types=1);

namespace pocketmine\inventory;

use pocketmine\compat\PlayerBridge;
use pocketmine\item\Item;

class Inventory
{
    public const MAX_STACK = 64;

    /** @var array<int, Item> */
    private array $contents = [];
    /** @var object[] */
    private array $viewers = [];
    /** @var object[] */
    private array $listeners = [];
    private int $maxStackSize = self::MAX_STACK;

    public function __construct(private int $size = 36, private ?PlayerBridge $bridge = null) {}

    /** @return Item[] */
    public function addItem(Item ...$items): array
    {
        $leftovers = [];
        foreach ($items as $item) {
            $remaining = clone $item;
            foreach ($this->contents as $slot => $existing) {
                if ($remaining->getCount() <= 0) {
                    break;
                }
                if (!$this->itemsMatch($existing, $remaining)) {
                    continue;
                }
                $space = min($this->maxStackSize, self::MAX_STACK) - $existing->getCount();
                if ($space <= 0) {
                    continue;
                }
                $move = min($space, $remaining->getCount());
                $this->contents[$slot] = (clone $existing)->setCount($existing->getCount() + $move);
                $this->syncSlot($slot);
                $remaining->setCount($remaining->getCount() - $move);
            }
            while ($remaining->getCount() > 0 && ($slot = $this->firstEmpty()) !== -1) {
                $move = min($this->maxStackSize, self::MAX_STACK, $remaining->getCount());
                $this->contents[$slot] = (clone $remaining)->setCount($move);
                $this->syncSlot($slot);
                $remaining->setCount($remaining->getCount() - $move);
            }
            if ($remaining->getCount() > 0) {
                $leftovers[] = $remaining;
            }
        }
        return $leftovers;
    }

    public function setItem(int $index, Item $item, bool $syncHost = true): void
    {
        if (!$this->slotExists($index)) {
            return;
        }
        if ($item->getCount() <= 0) {
            unset($this->contents[$index]);
            if ($syncHost) {
                $this->bridge?->clearInventorySlot($index);
            }
            return;
        }
        $this->contents[$index] = clone $item;
        if ($syncHost) {
            $this->syncSlot($index);
        }
    }

    public function getItem(int $index): Item
    {
        return isset($this->contents[$index]) ? clone $this->contents[$index] : new Item('minecraft:air', 'Air', 0);
    }

    /** @return array<int, Item> */
    public function getContents(bool $includeEmpty = false): array
    {
        if (!$includeEmpty) {
            return $this->contents;
        }
        $contents = [];
        for ($slot = 0; $slot < $this->size; $slot++) {
            $contents[$slot] = $this->getItem($slot);
        }
        return $contents;
    }

    /** @param array<int, Item> $items */
    public function setContents(array $items): void
    {
        $this->replaceContents($items, true);
    }

    /** @param array<int, Item> $items */
    public function replaceContents(array $items, bool $syncHost = false): void
    {
        $this->contents = [];
        if ($syncHost) {
            $this->bridge?->clearInventory();
        }
        foreach ($items as $slot => $item) {
            if (is_int($slot)) {
                $this->setItem($slot, $item, $syncHost);
            }
        }
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getMaxStackSize(): int
    {
        return $this->maxStackSize;
    }

    public function setMaxStackSize(int $size): void
    {
        $this->maxStackSize = max(1, $size);
    }

    public function canAddItem(Item $item): bool
    {
        return $this->getAddableItemQuantity($item) >= $item->getCount();
    }

    public function getAddableItemQuantity(Item $item): int
    {
        $quantity = 0;
        foreach ($this->contents as $existing) {
            if ($this->itemsMatch($existing, $item)) {
                $quantity += max(0, min($this->maxStackSize, self::MAX_STACK) - $existing->getCount());
            }
        }
        for ($slot = 0; $slot < $this->size; $slot++) {
            if (!isset($this->contents[$slot])) {
                $quantity += min($this->maxStackSize, self::MAX_STACK);
            }
        }
        return $quantity;
    }

    public function contains(Item $item): bool
    {
        $count = 0;
        foreach ($this->all($item) as $match) {
            $count += $match->getCount();
        }
        return $count >= $item->getCount();
    }

    /** @return array<int, Item> */
    public function all(Item $item): array
    {
        $matches = [];
        foreach ($this->contents as $slot => $existing) {
            if ($this->itemsMatch($existing, $item)) {
                $matches[$slot] = clone $existing;
            }
        }
        return $matches;
    }

    public function first(Item $item, bool $exact = false): int
    {
        foreach ($this->contents as $slot => $existing) {
            if ($this->itemsMatch($existing, $item) && (!$exact || $existing->getCount() === $item->getCount()) && $existing->getCount() >= $item->getCount()) {
                return $slot;
            }
        }
        return -1;
    }

    public function firstEmpty(): int
    {
        for ($slot = 0; $slot < $this->size; $slot++) {
            if (!isset($this->contents[$slot])) {
                return $slot;
            }
        }
        return -1;
    }

    public function isSlotEmpty(int $index): bool
    {
        return !$this->slotExists($index) || !isset($this->contents[$index]) || $this->contents[$index]->getCount() <= 0;
    }

    public function remove(Item $item): void
    {
        foreach (array_keys($this->all($item)) as $slot) {
            unset($this->contents[$slot]);
            $this->bridge?->clearInventorySlot($slot);
        }
    }

    /** @return Item[] */
    public function removeItem(Item ...$items): array
    {
        $leftovers = [];
        foreach ($items as $item) {
            $remaining = clone $item;
            foreach ($this->contents as $slot => $existing) {
                if ($remaining->getCount() <= 0) {
                    break;
                }
                if (!$this->itemsMatch($existing, $remaining)) {
                    continue;
                }
                $remove = min($existing->getCount(), $remaining->getCount());
                $newCount = $existing->getCount() - $remove;
                if ($newCount <= 0) {
                    unset($this->contents[$slot]);
                    $this->bridge?->clearInventorySlot($slot);
                } else {
                    $this->contents[$slot] = (clone $existing)->setCount($newCount);
                    $this->syncSlot($slot);
                }
                $remaining->setCount($remaining->getCount() - $remove);
            }
            if ($remaining->getCount() > 0) {
                $leftovers[] = $remaining;
            }
        }
        return $leftovers;
    }

    public function clear(int $index): void
    {
        unset($this->contents[$index]);
        $this->bridge?->clearInventorySlot($index);
    }

    public function swap(int $slot1, int $slot2): void
    {
        if (!$this->slotExists($slot1) || !$this->slotExists($slot2)) {
            return;
        }
        $first = $this->contents[$slot1] ?? null;
        $second = $this->contents[$slot2] ?? null;
        if ($second === null) {
            unset($this->contents[$slot1]);
            $this->bridge?->clearInventorySlot($slot1);
        } else {
            $this->contents[$slot1] = $second;
            $this->syncSlot($slot1);
        }
        if ($first === null) {
            unset($this->contents[$slot2]);
            $this->bridge?->clearInventorySlot($slot2);
        } else {
            $this->contents[$slot2] = $first;
            $this->syncSlot($slot2);
        }
    }

    /** @return object[] */
    public function getViewers(): array
    {
        return array_values($this->viewers);
    }

    public function onOpen(object $who): void
    {
        $this->viewers[spl_object_id($who)] = $who;
    }

    public function onClose(object $who): void
    {
        unset($this->viewers[spl_object_id($who)]);
    }

    public function slotExists(int $slot): bool
    {
        return $slot >= 0 && $slot < $this->size;
    }

    /** @return object[] */
    public function getListeners(): array
    {
        return $this->listeners;
    }

    public function addListener(object $listener): void
    {
        $this->listeners[spl_object_id($listener)] = $listener;
    }

    /** @return array<int, Item> */
    public function slots(): array
    {
        return $this->contents;
    }

    public function clearAll(): void
    {
        $this->contents = [];
        $this->bridge?->clearInventory();
    }

    private function itemsMatch(Item $a, Item $b): bool
    {
        return $a->getTypeId() === $b->getTypeId();
    }

    private function syncSlot(int $slot): void
    {
        if (isset($this->contents[$slot])) {
            $this->bridge?->setInventoryItem($slot, $this->contents[$slot]);
        } else {
            $this->bridge?->clearInventorySlot($slot);
        }
    }
}
