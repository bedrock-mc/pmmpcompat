<?php

declare(strict_types=1);

namespace pocketmine\event\inventory;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;
use pocketmine\inventory\transaction\InventoryTransaction;

class InventoryTransactionEvent extends Event implements Cancellable
{
    use CancellableTrait;

    public function __construct(private InventoryTransaction $transaction) {}

    public function getTransaction(): InventoryTransaction
    {
        return $this->transaction;
    }
}
