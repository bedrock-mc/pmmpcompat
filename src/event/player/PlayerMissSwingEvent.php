<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;

class PlayerMissSwingEvent extends PlayerEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(Player $player)
    {
        parent::__construct($player);
    }
}
