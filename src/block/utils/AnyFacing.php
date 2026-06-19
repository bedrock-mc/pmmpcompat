<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

interface AnyFacing
{
    public function getFacing(mixed ...$args): mixed;
    public function setFacing(mixed ...$args): mixed;
}
