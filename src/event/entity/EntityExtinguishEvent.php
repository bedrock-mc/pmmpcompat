<?php

declare(strict_types=1);

namespace pocketmine\event\entity;

use pocketmine\entity\Entity;

class EntityExtinguishEvent extends EntityEvent
{
    public const CAUSE_CUSTOM = 0;
    public const CAUSE_WATER = 1;
    public const CAUSE_WATER_CAULDRON = 2;
    public const CAUSE_RESPAWN = 3;
    public const CAUSE_FIRE_PROOF = 4;
    public const CAUSE_TICKING = 5;
    public const CAUSE_RAIN = 6;
    public const CAUSE_POWDER_SNOW = 7;

    public function __construct(
        Entity $entity,
        private int $cause,
    ) {
        $this->entity = $entity;
    }

    public function getCause(): int
    {
        return $this->cause;
    }
}
