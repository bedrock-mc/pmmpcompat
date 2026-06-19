<?php

declare(strict_types=1);

namespace pocketmine\item;

class Durable extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:durable', 'Durable'); }
    public function applyDamage(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getDamage(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getMaxDurability(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isBroken(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isUnbreakable(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setDamage(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setUnbreakable(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
