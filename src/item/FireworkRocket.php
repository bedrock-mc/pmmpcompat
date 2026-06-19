<?php

declare(strict_types=1);

namespace pocketmine\item;

class FireworkRocket extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:fireworkrocket', 'FireworkRocket'); }
    public const TAG_EXPLOSIONS = 0;
    public const TAG_FIREWORK_DATA = 0;
    public function getExplosions(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getFlightTimeMultiplier(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onInteractBlock(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function setExplosions(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setFlightTimeMultiplier(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
