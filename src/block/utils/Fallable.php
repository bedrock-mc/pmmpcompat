<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

interface Fallable
{
    public function getFallDamagePerBlock(mixed ...$args): mixed;
    public function getLandSound(mixed ...$args): mixed;
    public function getMaxFallDamage(mixed ...$args): mixed;
    public function onHitGround(mixed ...$args): mixed;
    public function tickFalling(mixed ...$args): mixed;
}
