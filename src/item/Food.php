<?php

declare(strict_types=1);

namespace pocketmine\item;

class Food extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:food', 'Food'); }
    public function canStartUsingItem(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getAdditionalEffects(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getResidue(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onConsume(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function requiresHunger(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
