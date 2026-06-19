<?php

declare(strict_types=1);

namespace pocketmine\item;

class ChorusFruit extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:chorusfruit', 'ChorusFruit'); }
    public function getCooldownTag(): ?string { return $this->compatMethod(__FUNCTION__, []); }
    public function getCooldownTicks(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getFoodRestore(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getSaturationRestore(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onConsume(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function requiresHunger(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
