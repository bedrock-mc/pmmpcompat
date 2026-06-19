<?php

declare(strict_types=1);

namespace pocketmine\block\inventory;

interface BlockInventory
{
    public function getHolder(): \pocketmine\world\Position;
}
