<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

use pocketmine\color\Color;
use pocketmine\utils\LegacyEnumShimTrait;

enum MobHeadType
{
    use LegacyEnumShimTrait;

    case SKELETON;
    case WITHER_SKELETON;
    case ZOMBIE;
    case PLAYER;
    case CREEPER;
    case DRAGON;
    case PIGLIN;

    public function getDisplayName(mixed ...$args): mixed { return ucwords(strtolower(str_replace('_', ' ', $this->name))); }
}
