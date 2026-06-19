<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\Event;
use pocketmine\player\Player;

class PlayerEvent extends Event
{
    public function __construct(protected Player $player) {}

    public function getPlayer(): Player
    {
        return $this->player;
    }
}
