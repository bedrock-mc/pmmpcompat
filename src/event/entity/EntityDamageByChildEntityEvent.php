<?php

declare(strict_types=1);

namespace pocketmine\event\entity;

use pocketmine\entity\Entity;

class EntityDamageByChildEntityEvent extends EntityDamageByEntityEvent
{
    /** @param float[] $modifiers */
    public function __construct(Entity $damager, private Entity $childEntity, Entity $entity, int $cause, float $damage, array $modifiers = [])
    {
        parent::__construct($damager, $entity, $cause, $damage, $modifiers);
    }

    public function getChild(): ?Entity
    {
        return $this->childEntity;
    }
}
