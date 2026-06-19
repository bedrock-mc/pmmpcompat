<?php

declare(strict_types=1);

namespace pocketmine\block;

class Magma extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:magma', 'Magma'); }
    public function burnsForever(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function getLightLevel(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function hasEntityCollision(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function onEntityInside(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
}
