<?php

declare(strict_types=1);

namespace pocketmine\entity\projectile;

class Snowball extends Throwable
{
    public static function getNetworkTypeId(mixed ...$args): string
    {
        return 'minecraft:snowball';
    }
}
