<?php

declare(strict_types=1);

namespace pocketmine\event\entity;

use pocketmine\entity\Entity;

class EntitySpawnEvent extends EntityEvent
{
    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
    }
}
