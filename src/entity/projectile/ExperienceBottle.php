<?php

declare(strict_types=1);

namespace pocketmine\entity\projectile;

class ExperienceBottle extends Throwable
{
    private bool $hit = false;
    private int $experienceDrop = 0;

    public static function getNetworkTypeId(mixed ...$args): string { return 'minecraft:xp_bottle'; }
    public function getResultDamage(mixed ...$args): int { return -1; }
    public function onHit(mixed ...$args): void
    {
        $this->hit = true;
        $this->experienceDrop = is_int($args[0] ?? null) ? $args[0] : 3;
        $this->flagForDespawn();
    }
    public function hasHit(): bool { return $this->hit; }
    public function getExperienceDrop(): int { return $this->experienceDrop; }
}
