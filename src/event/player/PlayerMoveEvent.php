<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class PlayerMoveEvent extends PlayerEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(Player $player, private Vector3 $from, private Vector3 $to)
    {
        parent::__construct($player);
    }

    public function getFrom(): Vector3 { return $this->from; }
    public function getTo(): Vector3 { return $this->to; }
    public function setTo(Vector3 $to): void { $this->to = $to; }
}
