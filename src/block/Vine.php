<?php

declare(strict_types=1);

namespace pocketmine\block;

class Vine extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:vine', 'Vine'); }
    public function canBeReplaced(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function canClimb(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function getDrops(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function getFaces(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getFlameEncouragement(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getFlammability(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function hasEntityCollision(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function hasFace(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onEntityInside(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onNearbyBlockChange(): void { $this->compatMethod(__FUNCTION__, []); }
    public function onRandomTick(): void { $this->compatMethod(__FUNCTION__, []); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function setFace(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setFaces(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function ticksRandomly(): bool { return $this->compatMethod(__FUNCTION__, []); }
}
