<?php

declare(strict_types=1);

namespace pocketmine\entity\projectile;

class EnderPearl extends Throwable
{
    private bool $hit = false;

    public static function getNetworkTypeId(mixed ...$args): string { return 'minecraft:ender_pearl'; }
    public function onHit(mixed ...$args): void
    {
        $this->hit = true;
        $this->flagForDespawn();
    }
    public function hasHit(): bool { return $this->hit; }
}
