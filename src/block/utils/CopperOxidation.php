<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

use pocketmine\color\Color;
use pocketmine\utils\LegacyEnumShimTrait;

enum CopperOxidation
{
    use LegacyEnumShimTrait;

    case NONE;
    case EXPOSED;
    case WEATHERED;
    case OXIDIZED;

    public function getNext(mixed ...$args): mixed { $cases = self::cases(); $i = array_search($this, $cases, true); return $cases[min(count($cases) - 1, $i + 1)] ?? $this; }

    public function getPrevious(mixed ...$args): mixed { $cases = self::cases(); $i = array_search($this, $cases, true); return $cases[max(0, $i - 1)] ?? $this; }
}
