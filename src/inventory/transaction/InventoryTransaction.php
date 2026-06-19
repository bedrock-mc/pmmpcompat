<?php

declare(strict_types=1);

namespace pocketmine\inventory\transaction;

use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\transaction\action\InventoryAction;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\player\Player;

class InventoryTransaction
{
    protected bool $hasExecuted = false;

    /** @var array<int, Inventory> */
    protected array $inventories = [];

    /** @var array<int, InventoryAction> */
    protected array $actions = [];

    /** @param InventoryAction[] $actions */
    public function __construct(protected Player $source, array $actions = [])
    {
        foreach ($actions as $action) {
            $this->addAction($action);
        }
    }

    public function getSource(): Player
    {
        return $this->source;
    }

    /** @return Inventory[] */
    public function getInventories(): array
    {
        return array_values($this->inventories);
    }

    /** @return InventoryAction[] */
    public function getActions(): array
    {
        return array_values($this->actions);
    }

    public function addAction(InventoryAction $action): void
    {
        $id = spl_object_id($action);
        if (isset($this->actions[$id])) {
            throw new \InvalidArgumentException('Tried to add the same action to a transaction twice');
        }
        $this->actions[$id] = $action;
        $action->onAddToTransaction($this);
        if ($action instanceof SlotChangeAction) {
            $inventory = $action->getInventory();
            $this->inventories[spl_object_id($inventory)] = $inventory;
        }
    }

    public function validate(): void
    {
        if ($this->actions === []) {
            throw new TransactionValidationException('Inventory transaction must have at least one action to be executable');
        }
        foreach ($this->actions as $action) {
            $action->validate($this->source);
        }
    }

    protected function callExecuteEvent(): bool
    {
        $ev = new InventoryTransactionEvent($this);
        $ev->call();
        return !$ev->isCancelled();
    }

    public function execute(): void
    {
        if ($this->hasExecuted) {
            throw new TransactionValidationException('Transaction has already been executed');
        }
        $this->validate();
        if (!$this->callExecuteEvent()) {
            throw new TransactionCancelledException('Transaction event cancelled');
        }
        foreach ($this->actions as $action) {
            if (!$action->onPreExecute($this->source)) {
                throw new TransactionCancelledException('One of the actions in this transaction was cancelled');
            }
        }
        foreach ($this->actions as $action) {
            $action->execute($this->source);
        }
        $this->hasExecuted = true;
    }

    public function hasExecuted(): bool
    {
        return $this->hasExecuted;
    }
}
