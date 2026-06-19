<?php

declare(strict_types=1);

namespace pocketmine\block;

class CaveVines extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:cavevines', 'CaveVines'); }
    public const MAX_AGE = 0;
    public function asItem(): \pocketmine\item\Item { return $this->compatMethod(__FUNCTION__, []); }
    public function canClimb(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function getDropsForCompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function getLightLevel(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function hasBerries(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function hasEntityCollision(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function isAffectedBySilkTouch(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function isHead(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onEntityInside(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onRandomTick(): void { $this->compatMethod(__FUNCTION__, []); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function setBerries(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setHead(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function ticksRandomly(): bool { return $this->compatMethod(__FUNCTION__, []); }
}
