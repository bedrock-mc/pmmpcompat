<?php

declare(strict_types=1);

namespace pocketmine\inventory\transaction\action\validator;

use pocketmine\inventory\Inventory;
use pocketmine\inventory\transaction\TransactionValidationException;
use pocketmine\item\Item;

interface SlotValidator
{
    public function validate(Inventory $inventory, Item $item, int $slot): ?TransactionValidationException;
}
