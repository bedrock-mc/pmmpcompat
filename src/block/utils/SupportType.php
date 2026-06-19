<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

use pocketmine\color\Color;
use pocketmine\utils\LegacyEnumShimTrait;

enum SupportType
{
    use LegacyEnumShimTrait;

    case FULL;
    case CENTER;
    case EDGE;
    case NONE;

    public function hasCenterSupport(mixed ...$args): mixed { return $this->name === 'CENTER' || $this->name === 'FULL'; }

    public function hasEdgeSupport(mixed ...$args): mixed { return $this->name === 'EDGE' || $this->name === 'FULL'; }
}
