<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

interface Lightable
{
    public function isLit(mixed ...$args): mixed;
    public function setLit(mixed ...$args): mixed;
}
