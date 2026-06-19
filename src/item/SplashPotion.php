<?php

declare(strict_types=1);

namespace pocketmine\item;

class SplashPotion extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:splashpotion', 'SplashPotion'); }
    public function getMaxStackSize(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getThrowForce(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function willLinger(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
