<?php

declare(strict_types=1);

namespace pocketmine\inventory\transaction\action\validator;

use pocketmine\inventory\Inventory;
use pocketmine\inventory\transaction\TransactionValidationException;
use pocketmine\item\Item;

class CallbackSlotValidator implements SlotValidator
{
    /** @param \Closure(Inventory, Item, int): ?TransactionValidationException $validate */
    public function __construct(private \Closure $validate) {}

    public function validate(Inventory $inventory, Item $item, int $slot): ?TransactionValidationException
    {
        return ($this->validate)($inventory, $item, $slot);
    }
}
