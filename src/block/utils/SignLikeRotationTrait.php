<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

trait SignLikeRotationTrait
{
    public function getRotation(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setRotation(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
