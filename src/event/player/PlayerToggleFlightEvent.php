<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;

class PlayerToggleFlightEvent extends PlayerEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(Player $player, protected bool $isFlying)
    {
        parent::__construct($player);
    }

    public function isFlying(): bool
    {
        return $this->isFlying;
    }
}
