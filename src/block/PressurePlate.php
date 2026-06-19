<?php

declare(strict_types=1);

namespace pocketmine\block;

class PressurePlate extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:pressureplate', 'PressurePlate'); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function hasEntityCollision(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function isSolid(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function onEntityInside(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onScheduledUpdate(): void { $this->compatMethod(__FUNCTION__, []); }
}
