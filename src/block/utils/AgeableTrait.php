<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

trait AgeableTrait
{
    public function getAge(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getMaxAge(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setAge(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
