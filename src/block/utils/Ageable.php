<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

interface Ageable
{
    public function getAge(mixed ...$args): mixed;
    public function getMaxAge(mixed ...$args): mixed;
    public function setAge(mixed ...$args): mixed;
}
