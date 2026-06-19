<?php

declare(strict_types=1);

namespace pocketmine\item;

enum FireworkRocketType
{
    case STUB;
    public function getExplosionSound(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
