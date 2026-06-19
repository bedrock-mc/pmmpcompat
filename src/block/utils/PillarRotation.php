<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

interface PillarRotation
{
    public function getAxis(mixed ...$args): mixed;
    public function setAxis(mixed ...$args): mixed;
}
