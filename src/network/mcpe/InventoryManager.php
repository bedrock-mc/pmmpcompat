<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe;

class InventoryManager
{
    private ?int $currentWindowId = null;
    private ?int $currentItemStackRequestId = null;
    /** @var array<int, mixed> */
    private array $windows = [];
    /** @var array<int, mixed> */
    private array $containerOpenCallbacks = [];
    /** @var list<array{window: int|null, slot: int, stack: mixed}> */
    private array $pendingSlotChanges = [];
    /** @var list<array{name: string, args: array<int, mixed>}> */
    private array $syncEvents = [];

    public function __construct(private mixed $session = null) {}

    public function getCurrentWindowId(): ?int { return $this->currentWindowId; }
    public function setCurrentItemStackRequestId(?int $requestId): void { $this->currentItemStackRequestId = $requestId; }
    public function getContainerOpenCallbacks(): array { return $this->containerOpenCallbacks; }
    public function getEnchantingTableOptionIndex(mixed $option): int { return is_int($option) ? $option : 0; }
    public function getItemStackInfo(mixed $itemStack): ItemStackInfo { return new ItemStackInfo($this->currentItemStackRequestId ?? 0, spl_object_id((object) $itemStack)); }

    public function getWindowId(mixed $inventory): ?int
    {
        foreach ($this->windows as $id => $window) {
            if ($window === $inventory) {
                return $id;
            }
        }
        return null;
    }

    /** @return array{mixed, int}|null */
    public function locateWindowAndSlot(int $windowId, int $networkSlot): ?array
    {
        return isset($this->windows[$windowId]) ? [$this->windows[$windowId], $networkSlot] : null;
    }

    public function onClientOpenMainInventory(): void { $this->currentWindowId = 0; }
    public function onClientRemoveWindow(int $windowId): void { unset($this->windows[$windowId]); if ($this->currentWindowId === $windowId) { $this->currentWindowId = null; } }
    public function onClientSelectHotbarSlot(int $slot): void { $this->syncEvents[] = ['name' => __FUNCTION__, 'args' => [$slot]]; }
    public function onCurrentWindowChange(?int $windowId): void { $this->currentWindowId = $windowId; }
    public function onCurrentWindowRemove(): void { $this->currentWindowId = null; }
    public function onSlotChange(mixed $inventory, int $slot, mixed $oldItem = null): void { $this->addPredictedSlotChange($this->getWindowId($inventory), $slot, $oldItem); }

    public function addPredictedSlotChange(?int $windowId, int $slot, mixed $stack): void { $this->pendingSlotChanges[] = ['window' => $windowId, 'slot' => $slot, 'stack' => $stack]; }
    public function addRawPredictedSlotChanges(array $changes): void { foreach ($changes as $change) { $this->pendingSlotChanges[] = (array) $change + ['window' => null, 'slot' => 0, 'stack' => null]; } }
    public function addTransactionPredictedSlotChanges(mixed $transaction): void { $this->syncEvents[] = ['name' => __FUNCTION__, 'args' => [$transaction]]; }
    public function flushPendingUpdates(): array { $changes = $this->pendingSlotChanges; $this->pendingSlotChanges = []; return $changes; }
    public function requestSyncAll(): void { $this->syncAll(); }
    public function syncAll(): void { $this->syncEvents[] = ['name' => __FUNCTION__, 'args' => []]; }
    public function syncContents(mixed ...$args): void { $this->syncEvents[] = ['name' => __FUNCTION__, 'args' => $args]; }
    public function syncCreative(mixed ...$args): void { $this->syncEvents[] = ['name' => __FUNCTION__, 'args' => $args]; }
    public function syncData(mixed ...$args): void { $this->syncEvents[] = ['name' => __FUNCTION__, 'args' => $args]; }
    public function syncEnchantingTableOptions(mixed ...$args): void { $this->syncEvents[] = ['name' => __FUNCTION__, 'args' => $args]; }
    public function syncMismatchedPredictedSlotChanges(): array { return $this->flushPendingUpdates(); }
    public function syncSelectedHotbarSlot(mixed ...$args): void { $this->syncEvents[] = ['name' => __FUNCTION__, 'args' => $args]; }
    public function syncSlot(mixed ...$args): void { $this->syncEvents[] = ['name' => __FUNCTION__, 'args' => $args]; }

    /** @return list<array{name: string, args: array<int, mixed>}> */
    public function getSyncEvents(): array { return $this->syncEvents; }
}
