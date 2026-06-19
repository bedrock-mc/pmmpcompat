<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

interface WoodMaterial
{
    public function getWoodType(mixed ...$args): mixed;
}
