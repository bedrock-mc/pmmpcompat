<?php

declare(strict_types=1);

namespace pocketmine\item;

class PitcherPod extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:pitcherpod', 'PitcherPod'); }
    public function getBlock(): \pocketmine\block\Block { return $this->compatMethod(__FUNCTION__, []); }
}
