<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

use pocketmine\color\Color;
use pocketmine\utils\LegacyEnumShimTrait;

enum CoralType
{
    use LegacyEnumShimTrait;

    case TUBE;
    case BRAIN;
    case BUBBLE;
    case FIRE;
    case HORN;

    public function getDisplayName(mixed ...$args): mixed { return ucwords(strtolower(str_replace('_', ' ', $this->name))); }
}
