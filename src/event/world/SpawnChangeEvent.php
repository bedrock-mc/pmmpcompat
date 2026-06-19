<?php

declare(strict_types=1);

namespace pocketmine\event\world;

use pocketmine\world\Position;
use pocketmine\world\World;

class SpawnChangeEvent extends WorldEvent
{
    public function __construct(World $world, private Position $previousSpawn)
    {
        parent::__construct($world);
    }

    public function getPreviousSpawn(): Position { return $this->previousSpawn; }
}
