<?php

declare(strict_types=1);

namespace pocketmine\event\entity;

use pocketmine\event\Event;

abstract class EntityEvent extends Event
{
    protected object $entity;

    public function __construct(?object $entity = null)
    {
        if ($entity !== null) {
            $this->entity = $entity;
        }
    }

    public function getEntity(): object
    {
        return $this->entity;
    }
}
