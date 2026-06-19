<?php

declare(strict_types=1);

namespace pocketmine\event\entity;

use pocketmine\entity\projectile\Projectile;
use pocketmine\math\RayTraceResult;

abstract class ProjectileHitEvent extends EntityEvent
{
    public function __construct(
        Projectile $entity,
        private RayTraceResult $rayTraceResult,
    ) {
        $this->entity = $entity;
    }

    public function getEntity(): Projectile
    {
        return $this->entity;
    }

    public function getRayTraceResult(): RayTraceResult
    {
        return $this->rayTraceResult;
    }
}
