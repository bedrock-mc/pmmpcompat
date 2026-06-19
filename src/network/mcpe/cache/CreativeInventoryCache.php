<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\cache;

use pocketmine\utils\SingletonTrait;

class CreativeInventoryCache
{
    use SingletonTrait;

    /** @var array<int, CreativeInventoryCacheEntry> */
    private array $caches = [];

    public function buildPacket(mixed $inventory, mixed $session = null): object
    {
        $entry = $this->getCacheEntry($inventory);
        return (object) [
            'inventory' => $inventory,
            'session' => $session,
            'groups' => $entry->groups,
            'items' => $entry->items,
        ];
    }

    private function getCacheEntry(mixed $inventory): CreativeInventoryCacheEntry
    {
        $id = is_object($inventory) ? spl_object_id($inventory) : crc32(serialize($inventory));
        return $this->caches[$id] ??= $this->buildCacheEntry($inventory);
    }

    private function buildCacheEntry(mixed $inventory): CreativeInventoryCacheEntry
    {
        $items = [];
        if (is_object($inventory) && method_exists($inventory, 'getAllEntries')) {
            foreach ($inventory->getAllEntries() as $index => $entry) {
                $items[] = ['netId' => $index, 'entry' => $entry];
            }
        } elseif (is_iterable($inventory)) {
            foreach ($inventory as $index => $item) {
                $items[] = ['netId' => $index, 'entry' => $item];
            }
        }
        return new CreativeInventoryCacheEntry([], [], $items);
    }
}
