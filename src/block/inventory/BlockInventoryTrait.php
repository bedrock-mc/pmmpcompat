<?php

declare(strict_types=1);

namespace pocketmine\block\inventory;

trait BlockInventoryTrait
{
    public function getHolder(): \pocketmine\world\Position { return $this->holder; }
}
