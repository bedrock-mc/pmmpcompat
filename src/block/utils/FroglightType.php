<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

use pocketmine\color\Color;
use pocketmine\utils\LegacyEnumShimTrait;

enum FroglightType
{
    use LegacyEnumShimTrait;

    case OCHRE;
    case PEARLESCENT;
    case VERDANT;
}
