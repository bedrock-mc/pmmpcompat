<?php

declare(strict_types=1);

namespace pocketmine\item;

class TurtleHelmet extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:turtlehelmet', 'TurtleHelmet'); }
    public function onTickWorn(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
}
