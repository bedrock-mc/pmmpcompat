<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

trait PoweredByRedstoneTrait
{
    public function isPowered(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setPowered(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
