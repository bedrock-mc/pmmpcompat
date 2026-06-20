<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;
use pocketmine\world\Position;

class PlayerMoveEvent extends PlayerEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(Player $player, private Position $from, private Position $to)
    {
        parent::__construct($player);
    }

    public function getFrom(): Position { return $this->from; }
    public function getTo(): Position { return $this->to; }
    public function setTo(Position $to): void { $this->to = $to; }
}
