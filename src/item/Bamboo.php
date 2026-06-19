<?php

declare(strict_types=1);

namespace pocketmine\item;

class Bamboo extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:bamboo', 'Bamboo'); }
    public function getBlock(): \pocketmine\block\Block { return $this->compatMethod(__FUNCTION__, []); }
    public function getFuelTime(): int { return $this->compatMethod(__FUNCTION__, []); }
}
