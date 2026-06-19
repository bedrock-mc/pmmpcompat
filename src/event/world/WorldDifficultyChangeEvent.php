<?php

declare(strict_types=1);

namespace pocketmine\event\world;

use pocketmine\world\World;

final class WorldDifficultyChangeEvent extends WorldEvent
{
    public function __construct(World $world, private int $oldDifficulty, private int $newDifficulty)
    {
        parent::__construct($world);
    }

    public function getOldDifficulty(): int { return $this->oldDifficulty; }
    public function getNewDifficulty(): int { return $this->newDifficulty; }
}
