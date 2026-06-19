<?php

declare(strict_types=1);

namespace pocketmine\block;

class RedstoneOre extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:redstoneore', 'RedstoneOre'); }
    public function getDropsForCompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function getLightLevel(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function isAffectedBySilkTouch(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onNearbyBlockChange(): void { $this->compatMethod(__FUNCTION__, []); }
    public function onRandomTick(): void { $this->compatMethod(__FUNCTION__, []); }
    public function ticksRandomly(): bool { return $this->compatMethod(__FUNCTION__, []); }
}
