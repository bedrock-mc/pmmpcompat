<?php

declare(strict_types=1);

namespace pocketmine\block;

class BlueIce extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:blueice', 'BlueIce'); }
    public function getDropsForCompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function getFrictionFactor(): float { return $this->compatMethod(__FUNCTION__, []); }
    public function isAffectedBySilkTouch(): bool { return $this->compatMethod(__FUNCTION__, []); }
}
