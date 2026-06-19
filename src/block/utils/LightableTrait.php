<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

trait LightableTrait
{
    public function isLit(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setLit(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
