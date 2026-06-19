<?php

declare(strict_types=1);

namespace pocketmine\entity\projectile;

class IceBomb extends Throwable
{
    private bool $hit = false;

    public static function getNetworkTypeId(mixed ...$args): string { return 'minecraft:ice_bomb'; }
    public function getResultDamage(mixed ...$args): int { return -1; }
    public function onHit(mixed ...$args): void
    {
        $this->hit = true;
        $this->flagForDespawn();
    }
    public function hasHit(): bool { return $this->hit; }
}
