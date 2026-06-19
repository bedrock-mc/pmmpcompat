<?php

declare(strict_types=1);

namespace pocketmine\event\entity;

use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

class EntityBlockChangeEvent extends EntityEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(
        Entity $entity,
        private Block $from,
        private Block $to,
    ) {
        $this->entity = $entity;
    }

    public function getBlock(): Block
    {
        return $this->from;
    }

    public function getTo(): Block
    {
        return $this->to;
    }
}
