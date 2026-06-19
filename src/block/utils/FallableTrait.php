<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

trait FallableTrait
{
    public function getFallDamagePerBlock(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getLandSound(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getMaxFallDamage(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onHitGround(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onNearbyBlockChange(): void { $this->compatMethod(__FUNCTION__, []); }
    public function tickFalling(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
