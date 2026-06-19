<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;

class PlayerToggleSprintEvent extends PlayerEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(Player $player, protected bool $isSprinting)
    {
        parent::__construct($player);
    }

    public function isSprinting(): bool
    {
        return $this->isSprinting;
    }
}
