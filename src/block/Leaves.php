<?php

declare(strict_types=1);

namespace pocketmine\block;

class Leaves extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:leaves', 'Leaves'); }
    public function blocksDirectSkyLight(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function getDropsForCompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function getFlameEncouragement(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getFlammability(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getLeavesType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isAffectedBySilkTouch(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function isCheckDecay(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isNoDecay(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onNearbyBlockChange(): void { $this->compatMethod(__FUNCTION__, []); }
    public function onRandomTick(): void { $this->compatMethod(__FUNCTION__, []); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function setCheckDecay(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setNoDecay(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function ticksRandomly(): bool { return $this->compatMethod(__FUNCTION__, []); }
}
