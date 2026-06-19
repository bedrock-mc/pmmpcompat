<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

use pocketmine\color\Color;
use pocketmine\utils\LegacyEnumShimTrait;

enum StairShape
{
    use LegacyEnumShimTrait;

    case STRAIGHT;
    case INNER_LEFT;
    case INNER_RIGHT;
    case OUTER_LEFT;
    case OUTER_RIGHT;
}
