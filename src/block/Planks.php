<?php

declare(strict_types=1);

namespace pocketmine\block;

class Planks extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:planks', 'Planks'); }
    public function getFlameEncouragement(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getFlammability(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getFuelTime(): int { return $this->compatMethod(__FUNCTION__, []); }
}
