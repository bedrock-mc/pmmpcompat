<?php

declare(strict_types=1);

namespace pocketmine\item;

class Shovel extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:shovel', 'Shovel'); }
    public function getAttackPoints(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getBlockToolHarvestLevel(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getBlockToolType(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function onAttackEntity(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onDestroyBlock(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
}
