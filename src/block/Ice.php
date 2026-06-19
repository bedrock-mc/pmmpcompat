<?php

declare(strict_types=1);

namespace pocketmine\block;

class Ice extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:ice', 'Ice'); }
    public function getDropsForCompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function getFrictionFactor(): float { return $this->compatMethod(__FUNCTION__, []); }
    public function getLightFilter(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function isAffectedBySilkTouch(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function onBreak(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onRandomTick(): void { $this->compatMethod(__FUNCTION__, []); }
    public function ticksRandomly(): bool { return $this->compatMethod(__FUNCTION__, []); }
}
