<?php

declare(strict_types=1);

namespace pocketmine\block;

class ChorusFlower extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:chorusflower', 'ChorusFlower'); }
    public const MAX_AGE = 0;
    public const MIN_AGE = 0;
    public function onProjectileHit(mixed ...$args): void { $this->compatMethod(__FUNCTION__, $args); }
    public function onRandomTick(): void { $this->compatMethod(__FUNCTION__, []); }
    public function ticksRandomly(): bool { return $this->compatMethod(__FUNCTION__, []); }
}
