<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

trait ColoredTrait
{
    public function describeBlockItemState(mixed ...$args): void { $this->compatMethod(__FUNCTION__, $args); }
    public function getColor(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setColor(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
