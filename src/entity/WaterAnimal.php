<?php

declare(strict_types=1);

namespace pocketmine\entity;

class WaterAnimal extends Living implements Ageable
{
    private bool $baby = false;

    public function canBreathe(mixed ...$args): bool { return $this->isUnderwater(); }
    public function isBaby(): bool { return $this->baby; }
    public function onAirExpired(mixed ...$args): mixed { return null; }
}
