<?php

declare(strict_types=1);

namespace pocketmine\event\entity;

use pocketmine\entity\object\ItemEntity;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

class ItemMergeEvent extends EntityEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(
        ItemEntity $entity,
        protected ItemEntity $target,
    ) {
        $this->entity = $entity;
    }

    public function getTarget(): ItemEntity
    {
        return $this->target;
    }
}
