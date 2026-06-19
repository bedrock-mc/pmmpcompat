<?php

declare(strict_types=1);

namespace pocketmine\item;

class SweetBerries extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:sweetberries', 'SweetBerries'); }
    public function getBlock(): \pocketmine\block\Block { return $this->compatMethod(__FUNCTION__, []); }
    public function getFoodRestore(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getSaturationRestore(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
