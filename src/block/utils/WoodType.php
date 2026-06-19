<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

use pocketmine\color\Color;
use pocketmine\utils\LegacyEnumShimTrait;

enum WoodType
{
    use LegacyEnumShimTrait;

    case OAK;
    case SPRUCE;
    case BIRCH;
    case JUNGLE;
    case ACACIA;
    case DARK_OAK;
    case MANGROVE;
    case CRIMSON;
    case WARPED;
    case CHERRY;
    case PALE_OAK;

    public function getAllSidedLogSuffix(mixed ...$args): mixed { return $this->name === 'CRIMSON' || $this->name === 'WARPED' ? 'Hyphae' : null; }

    public function getDisplayName(mixed ...$args): mixed { return ucwords(strtolower(str_replace('_', ' ', $this->name))); }

    public function getStandardLogSuffix(mixed ...$args): mixed { return $this->name === 'CRIMSON' || $this->name === 'WARPED' ? 'Stem' : null; }

    public function isFlammable(mixed ...$args): mixed { return $this->name !== 'CRIMSON' && $this->name !== 'WARPED'; }
}
