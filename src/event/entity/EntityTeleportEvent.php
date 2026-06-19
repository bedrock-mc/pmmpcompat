<?php

declare(strict_types=1);

namespace pocketmine\event\entity;

use pocketmine\entity\Entity;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\utils\Utils;
use pocketmine\world\Position;

class EntityTeleportEvent extends EntityEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(
        Entity $entity,
        private Position $from,
        private Position $to,
    ) {
        $this->entity = $entity;
    }

    public function getFrom(): Position
    {
        return $this->from;
    }

    public function getTo(): Position
    {
        return $this->to;
    }

    public function setTo(Position $to): void
    {
        Utils::checkVector3NotInfOrNaN($to);
        $this->to = $to;
    }
}
