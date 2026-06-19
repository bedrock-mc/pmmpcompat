<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

trait PillarRotationTrait
{
    public function getAxis(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function setAxis(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
