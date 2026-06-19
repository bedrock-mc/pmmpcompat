<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\player\Player;
use pocketmine\utils\Utils;
use pocketmine\world\Position;

class PlayerRespawnEvent extends PlayerEvent
{
    public function __construct(
        Player $player,
        protected Position $position,
    ) {
        parent::__construct($player);
    }

    public function getRespawnPosition(): Position
    {
        return $this->position;
    }

    public function setRespawnPosition(Position $position): void
    {
        if (!$position->isValid()) {
            throw new \InvalidArgumentException('Spawn position must reference a valid and loaded World');
        }
        Utils::checkVector3NotInfOrNaN($position);
        $this->position = $position;
    }
}
