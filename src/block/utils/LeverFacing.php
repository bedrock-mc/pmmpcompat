<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

use pocketmine\color\Color;
use pocketmine\utils\LegacyEnumShimTrait;

enum LeverFacing
{
    use LegacyEnumShimTrait;

    case UP_AXIS_X;
    case UP_AXIS_Z;
    case DOWN_AXIS_X;
    case DOWN_AXIS_Z;
    case NORTH;
    case EAST;
    case SOUTH;
    case WEST;

    public function getFacing(mixed ...$args): mixed { return array_search($this, self::cases(), true); }
}
