<?php

declare(strict_types=1);

namespace pocketmine\event\inventory;

use pocketmine\inventory\Inventory;
use pocketmine\player\Player;

class InventoryCloseEvent extends InventoryEvent
{
    public function __construct(
        Inventory $inventory,
        private Player $who,
    ) {
        parent::__construct($inventory);
    }

    public function getPlayer(): Player
    {
        return $this->who;
    }
}
