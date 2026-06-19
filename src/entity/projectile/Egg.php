<?php

declare(strict_types=1);

namespace pocketmine\entity\projectile;

class Egg extends Throwable
{
    public static function getNetworkTypeId(mixed ...$args): string
    {
        return 'minecraft:egg';
    }
}
