<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

use pocketmine\color\Color;
use pocketmine\utils\LegacyEnumShimTrait;

enum MushroomBlockType
{
    use LegacyEnumShimTrait;

    case PORES;
    case CAP_NORTHWEST;
    case CAP_NORTH;
    case CAP_NORTHEAST;
    case CAP_WEST;
    case CAP_MIDDLE;
    case CAP_EAST;
    case CAP_SOUTHWEST;
    case CAP_SOUTH;
    case CAP_SOUTHEAST;
    case ALL_CAP;
}
