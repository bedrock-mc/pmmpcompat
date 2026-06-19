<?php

declare(strict_types=1);

namespace pocketmine\item;

class FireworkStar extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:fireworkstar', 'FireworkStar'); }
    public function getColor(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getCustomColor(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getExplosion(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setCustomColor(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setExplosion(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
