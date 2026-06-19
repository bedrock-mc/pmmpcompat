<?php

declare(strict_types=1);

namespace pocketmine\inventory\transaction;

use pocketmine\inventory\Inventory;
use pocketmine\inventory\transaction\action\InventoryAction;

final class TransactionBuilder
{
    /** @var array<int, TransactionBuilderInventory> */
    private array $inventories = [];

    /** @var array<int, InventoryAction> */
    private array $extraActions = [];

    public function addAction(InventoryAction $action): void
    {
        $this->extraActions[spl_object_id($action)] = $action;
    }

    public function getInventory(Inventory $inventory): TransactionBuilderInventory
    {
        return $this->inventories[spl_object_id($inventory)] ??= new TransactionBuilderInventory($inventory);
    }

    /** @return InventoryAction[] */
    public function generateActions(): array
    {
        $actions = $this->extraActions;
        foreach ($this->inventories as $inventory) {
            foreach ($inventory->generateActions() as $action) {
                $actions[spl_object_id($action)] = $action;
            }
        }
        return array_values($actions);
    }
}
