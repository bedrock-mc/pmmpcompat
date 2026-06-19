<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

interface SignLikeRotation
{
    public function getRotation(mixed ...$args): mixed;
    public function setRotation(mixed ...$args): mixed;
}
