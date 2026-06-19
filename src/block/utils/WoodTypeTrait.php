<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

trait WoodTypeTrait
{
    public function __construct(mixed ...$args) { $this->compatMethod(__FUNCTION__, $args); }
    public function getWoodType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
