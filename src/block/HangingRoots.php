<?php

declare(strict_types=1);

namespace pocketmine\block;

class HangingRoots extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:hangingroots', 'HangingRoots'); }
    public function getDropsForIncompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
}
