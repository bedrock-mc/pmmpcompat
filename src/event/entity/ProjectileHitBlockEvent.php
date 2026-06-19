<?php

declare(strict_types=1);

namespace pocketmine\event\entity;

use pocketmine\block\Block;
use pocketmine\entity\projectile\Projectile;
use pocketmine\math\RayTraceResult;

class ProjectileHitBlockEvent extends ProjectileHitEvent
{
    public function __construct(
        Projectile $entity,
        RayTraceResult $rayTraceResult,
        private Block $blockHit,
    ) {
        parent::__construct($entity, $rayTraceResult);
    }

    public function getBlockHit(): Block
    {
        return $this->blockHit;
    }
}
