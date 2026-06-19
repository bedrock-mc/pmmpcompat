<?php

declare(strict_types=1);

namespace pocketmine\block;

class FenceGate extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:fencegate', 'FenceGate'); }
    public function getFlameEncouragement(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getFlammability(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getFuelTime(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isInWall(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isOpen(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onNearbyBlockChange(): void { $this->compatMethod(__FUNCTION__, []); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function setInWall(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setOpen(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
