<?php

declare(strict_types=1);

namespace pocketmine\block;

class Bed extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:bed', 'Bed'); }
    public function getAffectedBlocks(): array { return $this->compatMethod(__FUNCTION__, []); }
    public function getDrops(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function getMaxStackSize(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getOtherHalf(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isHeadPart(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isOccupied(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onEntityLand(mixed ...$args): ?float { return $this->compatMethod(__FUNCTION__, $args); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onNearbyBlockChange(): void { $this->compatMethod(__FUNCTION__, []); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function readStateFromWorld(): \pocketmine\block\Block { return $this->compatMethod(__FUNCTION__, []); }
    public function setHead(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setOccupied(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function writeStateToWorld(): void { $this->compatMethod(__FUNCTION__, []); }
}
