<?php

declare(strict_types=1);

namespace pocketmine\block;

class PitcherCrop extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:pitchercrop', 'PitcherCrop'); }
    public const MAX_AGE = 0;
    public function asItem(): \pocketmine\item\Item { return $this->compatMethod(__FUNCTION__, []); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onRandomTick(): void { $this->compatMethod(__FUNCTION__, []); }
    public function ticksRandomly(): bool { return $this->compatMethod(__FUNCTION__, []); }
}
