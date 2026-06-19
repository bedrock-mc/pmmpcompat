<?php

declare(strict_types=1);

namespace pocketmine\event\entity;

use pocketmine\block\Block;
use pocketmine\entity\Entity;

class EntityDamageByBlockEvent extends EntityDamageEvent
{
    /** @param float[] $modifiers */
    public function __construct(private Block $damager, Entity $entity, int $cause, float $damage, array $modifiers = [])
    {
        parent::__construct($entity, $damage, $cause, $modifiers);
    }

    public function getDamager(): Block
    {
        return $this->damager;
    }
}
