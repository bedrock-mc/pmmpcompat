<?php

declare(strict_types=1);

namespace pocketmine\event\entity;

use pocketmine\entity\object\ItemEntity;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

class ItemDespawnEvent extends EntityEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(ItemEntity $item)
    {
        $this->entity = $item;
    }

    public function getEntity(): ItemEntity
    {
        return $this->entity;
    }
}
