<?php

declare(strict_types=1);

namespace pocketmine\item;

class Potion extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:potion', 'Potion'); }
    public function canStartUsingItem(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getAdditionalEffects(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getMaxStackSize(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getResidue(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onConsume(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
