<?php

declare(strict_types=1);

namespace pocketmine\item;

class Trident extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:trident', 'Trident'); }
    public function canStartUsingItem(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getAttackPoints(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getMaxDurability(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onAttackEntity(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onDestroyBlock(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onReleaseUsing(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
}
