<?php

declare(strict_types=1);

namespace pocketmine\block;

class NetherWartPlant extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:netherwartplant', 'NetherWartPlant'); }
    public const MAX_AGE = 0;
    public function getDropsForCompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function onRandomTick(): void { $this->compatMethod(__FUNCTION__, []); }
    public function ticksRandomly(): bool { return $this->compatMethod(__FUNCTION__, []); }
}
