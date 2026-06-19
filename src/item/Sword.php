<?php

declare(strict_types=1);

namespace pocketmine\item;

class Sword extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:sword', 'Sword'); }
    public function getAttackPoints(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getBlockToolHarvestLevel(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getBlockToolType(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getMiningEfficiency(bool $isCorrectTool): float { return $this->compatMethod(__FUNCTION__, [$isCorrectTool]); }
    public function onAttackEntity(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onDestroyBlock(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
}
