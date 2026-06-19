<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

trait CoralTypeTrait
{
    public function describeBlockItemState(mixed ...$args): void { $this->compatMethod(__FUNCTION__, $args); }
    public function getCoralType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isDead(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setCoralType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setDead(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
