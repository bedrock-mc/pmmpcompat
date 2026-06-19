<?php

declare(strict_types=1);

namespace pocketmine\block;

class Mycelium extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:mycelium', 'Mycelium'); }
    public function getDropsForCompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function isAffectedBySilkTouch(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function onRandomTick(): void { $this->compatMethod(__FUNCTION__, []); }
    public function ticksRandomly(): bool { return $this->compatMethod(__FUNCTION__, []); }
}
