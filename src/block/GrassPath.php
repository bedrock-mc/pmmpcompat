<?php

declare(strict_types=1);

namespace pocketmine\block;

class GrassPath extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:grasspath', 'GrassPath'); }
    public function getDropsForCompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function isAffectedBySilkTouch(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function onNearbyBlockChange(): void { $this->compatMethod(__FUNCTION__, []); }
}
