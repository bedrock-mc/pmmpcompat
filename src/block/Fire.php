<?php

declare(strict_types=1);

namespace pocketmine\block;

class Fire extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:fire', 'Fire'); }
    public const MAX_AGE = 0;
    public function onNearbyBlockChange(): void { $this->compatMethod(__FUNCTION__, []); }
    public function onRandomTick(): void { $this->compatMethod(__FUNCTION__, []); }
    public function onScheduledUpdate(): void { $this->compatMethod(__FUNCTION__, []); }
    public function ticksRandomly(): bool { return $this->compatMethod(__FUNCTION__, []); }
}
