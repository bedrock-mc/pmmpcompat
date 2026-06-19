<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

use pocketmine\color\Color;
use pocketmine\utils\LegacyEnumShimTrait;

enum ChiseledBookshelfSlot
{
    use LegacyEnumShimTrait;

    case TOP_LEFT;
    case TOP_MIDDLE;
    case TOP_RIGHT;
    case BOTTOM_LEFT;
    case BOTTOM_MIDDLE;
    case BOTTOM_RIGHT;

    public static function fromBlockFaceCoordinates(mixed ...$args): mixed {
        $x = (float) ($args[0] ?? 0.0);
        $y = (float) ($args[1] ?? 0.0);
        if ($x < 0.0 || $x > 1.0 || $y < 0.0 || $y > 1.0) { throw new \InvalidArgumentException('coordinates must be between 0 and 1'); }
        $slot = ($y < 0.5 ? 3 : 0) + ($x < 6 / 16 ? 0 : ($x < 11 / 16 ? 1 : 2));
        return self::cases()[$slot];
    }
}
