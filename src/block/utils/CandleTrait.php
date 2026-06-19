<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

trait CandleTrait
{
    public function getLightLevel(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function onInteract(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onProjectileHit(mixed ...$args): void { $this->compatMethod(__FUNCTION__, $args); }
}
