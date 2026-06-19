<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

use pocketmine\color\Color;
use pocketmine\utils\LegacyEnumShimTrait;

enum BellAttachmentType
{
    use LegacyEnumShimTrait;

    case CEILING;
    case FLOOR;
    case ONE_WALL;
    case TWO_WALLS;
}
