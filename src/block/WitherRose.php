<?php

declare(strict_types=1);

namespace pocketmine\block;

class WitherRose extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:witherrose', 'WitherRose'); }
    public function getFlameEncouragement(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getFlammability(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function hasEntityCollision(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function onEntityInside(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
}
