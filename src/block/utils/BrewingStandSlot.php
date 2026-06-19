<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

use pocketmine\color\Color;
use pocketmine\utils\LegacyEnumShimTrait;

enum BrewingStandSlot
{
    use LegacyEnumShimTrait;

    case EAST;
    case NORTHWEST;
    case SOUTHWEST;

    public function getSlotNumber(mixed ...$args): mixed { return array_search($this, self::cases(), true); }
}
