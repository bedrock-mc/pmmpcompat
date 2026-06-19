<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

trait AnyFacingTrait
{
    public function getFacing(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setFacing(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
