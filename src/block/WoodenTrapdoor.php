<?php

declare(strict_types=1);

namespace pocketmine\block;

class WoodenTrapdoor extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:woodentrapdoor', 'WoodenTrapdoor'); }
    public function getFuelTime(): int { return $this->compatMethod(__FUNCTION__, []); }
}
