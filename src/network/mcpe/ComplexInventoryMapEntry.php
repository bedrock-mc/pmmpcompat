<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe;

use pocketmine\inventory\Inventory;

final class ComplexInventoryMapEntry
{
    /** @var array<int, int> */
    private array $reverseSlotMap = [];

    /** @param array<int, int> $slotMap */
    public function __construct(
        private Inventory $inventory,
        private array $slotMap,
    ) {
        foreach ($slotMap as $slot => $index) {
            $this->reverseSlotMap[$index] = $slot;
        }
    }

    public function getInventory(): Inventory
    {
        return $this->inventory;
    }

    /** @return array<int, int> */
    public function getSlotMap(): array
    {
        return $this->slotMap;
    }

    public function mapNetToCore(int $slot): ?int
    {
        return $this->slotMap[$slot] ?? null;
    }

    public function mapCoreToNet(int $slot): ?int
    {
        return $this->reverseSlotMap[$slot] ?? null;
    }
}
