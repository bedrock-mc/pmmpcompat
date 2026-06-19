<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block\convert\property;

use pocketmine\utils\SingletonTrait;

class CommonProperties
{
    use SingletonTrait;

    public BoolProperty $lit;
    public IntProperty $analogRedstoneSignal;
    public DummyProperty $dummyCardinalDirection;
    public DummyProperty $dummyPillarAxis;

    public function __construct()
    {
        $this->lit = new BoolProperty('lit', static fn(object $block): bool => (bool) ($block->lit ?? false), static function(object $block, bool $value): void { $block->lit = $value; });
        $this->analogRedstoneSignal = new IntProperty('redstone_signal', 0, 15, static fn(object $block): int => (int) ($block->redstoneSignal ?? 0), static function(object $block, int $value): void { $block->redstoneSignal = $value; });
        $this->dummyCardinalDirection = new DummyProperty('minecraft:cardinal_direction', 'north');
        $this->dummyPillarAxis = new DummyProperty('pillar_axis', 'y');
    }
}
