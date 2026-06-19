<?php

declare(strict_types=1);

namespace pocketmine\block;

class WoodenDoor extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:woodendoor', 'WoodenDoor'); }
    public function getFuelTime(): int { return $this->compatMethod(__FUNCTION__, []); }
}
