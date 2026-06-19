<?php

declare(strict_types=1);

namespace pocketmine\item;

class PumpkinSeeds extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:pumpkinseeds', 'PumpkinSeeds'); }
    public function getBlock(): \pocketmine\block\Block { return $this->compatMethod(__FUNCTION__, []); }
}
