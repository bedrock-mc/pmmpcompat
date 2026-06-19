<?php

declare(strict_types=1);

namespace pocketmine\event\entity;

class EntityDamageByEntityEvent extends EntityDamageEvent
{
    private float $knockBack = 0.4;
    private float $verticalKnockBackLimit = 0.4;

    /** @param array<int, float> $modifiers */
    public function __construct(
        private object $damager,
        object $entity,
        int $cause,
        float $baseDamage,
        array $modifiers = [],
        float $knockBack = 0.4,
        float $verticalKnockBackLimit = 0.4,
    ) {
        $this->knockBack = $knockBack;
        $this->verticalKnockBackLimit = $verticalKnockBackLimit;
        parent::__construct($entity, $baseDamage, $cause, $modifiers);
    }

    public function getDamager(): object
    {
        return $this->damager;
    }

    public function getKnockBack(): float { return $this->knockBack; }
    public function setKnockBack(float $knockBack): void { $this->knockBack = $knockBack; }
    public function getVerticalKnockBackLimit(): float { return $this->verticalKnockBackLimit; }
    public function setVerticalKnockBackLimit(float $verticalKnockBackLimit): void { $this->verticalKnockBackLimit = $verticalKnockBackLimit; }
}
