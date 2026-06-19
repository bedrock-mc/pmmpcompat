<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

use pocketmine\color\Color;
use pocketmine\utils\LegacyEnumShimTrait;

enum SaplingType
{
    use LegacyEnumShimTrait;

    case OAK;
    case SPRUCE;
    case BIRCH;
    case JUNGLE;
    case ACACIA;
    case DARK_OAK;

    public function getDisplayName(mixed ...$args): mixed { return ucwords(strtolower(str_replace('_', ' ', $this->name))); }

    public function getTreeType(mixed ...$args): mixed { return strtolower($this->name); }
}
