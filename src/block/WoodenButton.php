<?php

declare(strict_types=1);

namespace pocketmine\block;

class WoodenButton extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:woodenbutton', 'WoodenButton'); }
    public function getFuelTime(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function hasEntityCollision(): bool { return $this->compatMethod(__FUNCTION__, []); }
}
