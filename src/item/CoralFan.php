<?php

declare(strict_types=1);

namespace pocketmine\item;

class CoralFan extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:coralfan', 'CoralFan'); }
    public function getBlock(): \pocketmine\block\Block { return $this->compatMethod(__FUNCTION__, []); }
    public function getFuelTime(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getMaxStackSize(): int { return $this->compatMethod(__FUNCTION__, []); }
}
