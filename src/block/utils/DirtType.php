<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

use pocketmine\color\Color;
use pocketmine\utils\LegacyEnumShimTrait;

enum DirtType
{
    use LegacyEnumShimTrait;

    case NORMAL;
    case COARSE;
    case ROOTED;
}
