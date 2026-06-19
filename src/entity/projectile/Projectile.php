<?php

declare(strict_types=1);

namespace pocketmine\entity\projectile;

use pocketmine\entity\Entity;

class Projectile extends Entity
{
    private float $baseDamage = 0.0;

    public function __construct(mixed ...$args) {}

    public function getBaseDamage(): float
    {
        return $this->baseDamage;
    }

    public function setBaseDamage(float $damage): void
    {
        $this->baseDamage = $damage;
    }

    public function getResultDamage(): int
    {
        return (int) ceil($this->baseDamage);
    }

    public function attack(mixed ...$args): mixed { return null; }
    public function canBeCollidedWith(mixed ...$args): bool { return true; }
    public function canCollideWith(mixed ...$args): bool { return true; }
    public function hasMovementUpdate(mixed ...$args): bool { return false; }
    public function onNearbyBlockChange(mixed ...$args): mixed { return null; }
    public function saveNBT(mixed ...$args): mixed { return null; }
}
