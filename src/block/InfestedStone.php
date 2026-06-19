<?php

declare(strict_types=1);

namespace pocketmine\block;

class InfestedStone extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:infestedstone', 'InfestedStone'); }
    public function getDropsForCompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function getImitatedBlock(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getSilkTouchDrops(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function isAffectedBySilkTouch(): bool { return $this->compatMethod(__FUNCTION__, []); }
}
