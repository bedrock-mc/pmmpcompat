<?php

declare(strict_types=1);

namespace pocketmine\item;

class GoatHorn extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:goathorn', 'GoatHorn'); }
    public function canStartUsingItem(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getCooldownTag(): ?string { return $this->compatMethod(__FUNCTION__, []); }
    public function getCooldownTicks(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getHornType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getMaxStackSize(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function onClickAir(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function setHornType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
