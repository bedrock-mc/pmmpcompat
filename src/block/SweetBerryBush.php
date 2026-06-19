<?php

declare(strict_types=1);

namespace pocketmine\block;

class SweetBerryBush extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:sweetberrybush', 'SweetBerryBush'); }
    public const MAX_AGE = 0;
    public const STAGE_BUSH_NO_BERRIES = 0;
    public const STAGE_BUSH_SOME_BERRIES = 0;
    public const STAGE_MATURE = 0;
    public const STAGE_SAPLING = 0;
    public function asItem(): \pocketmine\item\Item { return $this->compatMethod(__FUNCTION__, []); }
    public function getBerryDropAmount(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getDropsForCompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function hasEntityCollision(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function onEntityInside(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onRandomTick(): void { $this->compatMethod(__FUNCTION__, []); }
    public function ticksRandomly(): bool { return $this->compatMethod(__FUNCTION__, []); }
}
