<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

trait AmethystTrait
{
    public function onProjectileHit(mixed ...$args): void { $this->compatMethod(__FUNCTION__, $args); }
}
