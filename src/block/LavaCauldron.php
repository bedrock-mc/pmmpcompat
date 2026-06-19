<?php

declare(strict_types=1);

namespace pocketmine\block;

class LavaCauldron extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:lavacauldron', 'LavaCauldron'); }
    public function getEmptySound(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getFillSound(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getLightLevel(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function hasEntityCollision(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function onEntityInside(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function writeStateToWorld(): void { $this->compatMethod(__FUNCTION__, []); }
}
