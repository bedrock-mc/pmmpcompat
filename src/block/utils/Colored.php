<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

interface Colored
{
    public function getColor(mixed ...$args): mixed;
    public function setColor(mixed ...$args): mixed;
}
