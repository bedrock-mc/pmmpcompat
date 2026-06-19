<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

trait CopperTrait
{
    public function describeBlockItemState(mixed ...$args): void { $this->compatMethod(__FUNCTION__, $args); }
    public function getOxidation(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isWaxed(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onInteract(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setOxidation(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setWaxed(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
