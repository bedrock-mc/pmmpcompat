<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

interface HorizontalFacing
{
    public function getFacing(mixed ...$args): mixed;
    public function setFacing(mixed ...$args): mixed;
}
