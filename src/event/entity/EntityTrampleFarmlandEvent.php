<?php

declare(strict_types=1);

namespace pocketmine\event\entity;

use pocketmine\block\Block;
use pocketmine\entity\Living;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

class EntityTrampleFarmlandEvent extends EntityEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(
        Living $entity,
        private Block $block,
    ) {
        $this->entity = $entity;
    }

    public function getBlock(): Block
    {
        return $this->block;
    }
}
