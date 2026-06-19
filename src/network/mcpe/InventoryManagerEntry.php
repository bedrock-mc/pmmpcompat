<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe;

use pocketmine\inventory\Inventory;

final class InventoryManagerEntry
{
    /** @var array<int, mixed> */
    public array $predictions = [];

    /** @var array<int, ItemStackInfo> */
    public array $itemStackInfos = [];

    /** @var array<int, mixed> */
    public array $pendingSyncs = [];

    public function __construct(
        public Inventory $inventory,
        public ?ComplexInventoryMapEntry $complexSlotMap = null,
    ) {
    }
}
