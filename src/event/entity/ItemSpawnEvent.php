<?php

declare(strict_types=1);

namespace pocketmine\event\entity;

use pocketmine\entity\object\ItemEntity;

class ItemSpawnEvent extends EntityEvent
{
    public function __construct(ItemEntity $item)
    {
        $this->entity = $item;
    }

    public function getEntity(): ItemEntity
    {
        return $this->entity;
    }
}
