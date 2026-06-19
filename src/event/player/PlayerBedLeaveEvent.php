<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\block\Block;
use pocketmine\player\Player;

class PlayerBedLeaveEvent extends PlayerEvent
{
    public function __construct(Player $player, private Block $bed)
    {
        parent::__construct($player);
    }

    public function getBed(): Block { return $this->bed; }
}
