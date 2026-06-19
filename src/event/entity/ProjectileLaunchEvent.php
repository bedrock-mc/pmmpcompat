<?php

declare(strict_types=1);

namespace pocketmine\event\entity;

use pocketmine\entity\projectile\Projectile;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

class ProjectileLaunchEvent extends EntityEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(Projectile $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity(): Projectile
    {
        return $this->entity;
    }
}
