<?php

declare(strict_types=1);

namespace pocketmine\item;

class ItemBlock extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:itemblock', 'ItemBlock'); }
    public function getBlock(): \pocketmine\block\Block { return $this->compatMethod(__FUNCTION__, []); }
    public function getFuelTime(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getMaxStackSize(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function isFireProof(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function isNull(): bool { return $this->compatMethod(__FUNCTION__, []); }
}
