<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\GameMode;
use pocketmine\player\Player;

class PlayerGameModeChangeEvent extends PlayerEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(Player $player, protected GameMode $newGamemode)
    {
        parent::__construct($player);
    }

    public function getNewGamemode(): GameMode
    {
        return $this->newGamemode;
    }
}
