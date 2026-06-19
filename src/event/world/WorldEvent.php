<?php

declare(strict_types=1);

namespace pocketmine\event\world;

use pocketmine\event\Event;
use pocketmine\world\World;

abstract class WorldEvent extends Event
{
    public function __construct(private World $world) {}

    public function getWorld(): World
    {
        return $this->world;
    }
}
