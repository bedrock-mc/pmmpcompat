<?php

declare(strict_types=1);

namespace pocketmine\event\entity;

use pocketmine\entity\Entity;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\math\Vector3;

class EntityMotionEvent extends EntityEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(
        Entity $entity,
        private Vector3 $mot,
    ) {
        $this->entity = $entity;
    }

    public function getVector(): Vector3
    {
        return $this->mot;
    }
}
