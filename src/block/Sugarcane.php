<?php

declare(strict_types=1);

namespace pocketmine\block;

class Sugarcane extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:sugarcane', 'Sugarcane'); }
    public const MAX_AGE = 0;
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onNearbyBlockChange(): void { $this->compatMethod(__FUNCTION__, []); }
    public function onRandomTick(): void { $this->compatMethod(__FUNCTION__, []); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function ticksRandomly(): bool { return $this->compatMethod(__FUNCTION__, []); }
}
