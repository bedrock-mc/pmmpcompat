<?php

declare(strict_types=1);

namespace pocketmine\inventory;

use pocketmine\item\Item;
use pocketmine\utils\ObjectSet;
use pocketmine\utils\SingletonTrait;

class CreativeInventory
{
    use SingletonTrait;

    /** @var array<int, CreativeInventoryEntry> */
    private array $creative = [];
    private ObjectSet $contentChangedCallbacks;

    public function __construct()
    {
        $this->contentChangedCallbacks = new ObjectSet();
    }

    public function add(Item $item, CreativeCategory $category = CreativeCategory::ITEMS, ?CreativeGroup $group = null): void
    {
        $this->creative[] = new CreativeInventoryEntry($item, $category, $group);
        $this->onContentChange();
    }

    public function clear(): void
    {
        $this->creative = [];
        $this->onContentChange();
    }

    public function contains(Item $item): bool { return $this->getItemIndex($item) !== -1; }
    /** @return Item[] */
    public function getAll(): array { return array_map(static fn(CreativeInventoryEntry $entry): Item => $entry->getItem(), $this->creative); }
    /** @return array<int, CreativeInventoryEntry> */
    public function getAllEntries(): array { return $this->creative; }
    public function getContentChangedCallbacks(): ObjectSet { return $this->contentChangedCallbacks; }
    public function getEntry(int $index): ?CreativeInventoryEntry { return $this->creative[$index] ?? null; }
    public function getItem(int $index): ?Item { return $this->getEntry($index)?->getItem(); }
    public function getItemIndex(Item $item): int
    {
        foreach ($this->creative as $index => $entry) {
            if ($entry->matchesItem($item)) {
                return $index;
            }
        }
        return -1;
    }

    public function remove(Item $item): void
    {
        $index = $this->getItemIndex($item);
        if ($index !== -1) {
            unset($this->creative[$index]);
            $this->creative = array_values($this->creative);
            $this->onContentChange();
        }
    }

    private function onContentChange(): void
    {
        foreach ($this->contentChangedCallbacks as $callback) {
            if ($callback instanceof \Closure) {
                $callback();
            }
        }
    }
}
