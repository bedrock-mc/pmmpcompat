<?php

declare(strict_types=1);

namespace pocketmine\block;

class DoublePitcherCrop extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:doublepitchercrop', 'DoublePitcherCrop'); }
    public const MAX_AGE = 0;
    public function asItem(): \pocketmine\item\Item { return $this->compatMethod(__FUNCTION__, []); }
    public function describeBlockOnlyState(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getDropsForCompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onRandomTick(): void { $this->compatMethod(__FUNCTION__, []); }
    public function ticksRandomly(): bool { return $this->compatMethod(__FUNCTION__, []); }
}
