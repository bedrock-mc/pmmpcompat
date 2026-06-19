<?php

declare(strict_types=1);

namespace pocketmine\inventory;

use pocketmine\utils\ObjectSet;

interface SlotValidatedInventory
{
    public function getSlotValidators(): ObjectSet;
}
