<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\player\Player;

class PlayerViewDistanceChangeEvent extends PlayerEvent
{
    public function __construct(Player $player, protected int $oldDistance, protected int $newDistance)
    {
        parent::__construct($player);
    }

    public function getNewDistance(): int { return $this->newDistance; }
    public function getOldDistance(): int { return $this->oldDistance; }
}
