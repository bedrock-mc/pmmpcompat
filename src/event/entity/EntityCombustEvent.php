<?php

declare(strict_types=1);

namespace pocketmine\event\entity;

use pocketmine\entity\Entity;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

class EntityCombustEvent extends EntityEvent implements Cancellable
{
    use CancellableTrait;

    protected int $duration;

    public function __construct(Entity $combustee, int $duration)
    {
        $this->entity = $combustee;
        $this->duration = $duration;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): void
    {
        $this->duration = $duration;
    }
}
