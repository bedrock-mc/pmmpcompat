<?php

declare(strict_types=1);

namespace pocketmine\event\entity;

use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Projectile;
use pocketmine\math\RayTraceResult;

class ProjectileHitEntityEvent extends ProjectileHitEvent
{
    public function __construct(
        Projectile $entity,
        RayTraceResult $rayTraceResult,
        private Entity $entityHit,
    ) {
        parent::__construct($entity, $rayTraceResult);
    }

    public function getEntityHit(): Entity
    {
        return $this->entityHit;
    }
}
