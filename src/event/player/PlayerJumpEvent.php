<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\player\Player;

class PlayerJumpEvent extends PlayerEvent
{
    public function __construct(Player $player)
    {
        parent::__construct($player);
    }
}
