<?php

declare(strict_types=1);

namespace pocketmine\item;

class HoneyBottle extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:honeybottle', 'HoneyBottle'); }
    public function getFoodRestore(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getMaxStackSize(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getResidue(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getSaturationRestore(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onConsume(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function requiresHunger(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
