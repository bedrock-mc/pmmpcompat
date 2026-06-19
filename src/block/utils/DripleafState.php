<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

use pocketmine\color\Color;
use pocketmine\utils\LegacyEnumShimTrait;

enum DripleafState
{
    use LegacyEnumShimTrait;

    case STABLE;
    case UNSTABLE;
    case PARTIAL_TILT;
    case FULL_TILT;

    public function getScheduledUpdateDelayTicks(mixed ...$args): mixed { return match($this->name) { 'STABLE' => null, 'FULL_TILT' => 100, default => 10 }; }
}
