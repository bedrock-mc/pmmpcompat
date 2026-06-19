<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

interface PoweredByRedstone
{
    public function isPowered(mixed ...$args): mixed;
    public function setPowered(mixed ...$args): mixed;
}
