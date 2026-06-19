<?php

declare(strict_types=1);

namespace pocketmine\block;

class Grass extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:grass', 'Grass'); }
    public function getDropsForCompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function isAffectedBySilkTouch(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onRandomTick(): void { $this->compatMethod(__FUNCTION__, []); }
    public function ticksRandomly(): bool { return $this->compatMethod(__FUNCTION__, []); }
}
