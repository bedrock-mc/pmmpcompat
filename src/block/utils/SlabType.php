<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

use pocketmine\color\Color;
use pocketmine\utils\LegacyEnumShimTrait;

enum SlabType
{
    use LegacyEnumShimTrait;

    case BOTTOM;
    case TOP;
    case DOUBLE;
}
