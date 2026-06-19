<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

class CropGrowthHelper
{
    /** @var list<mixed> */
    private array $args;

    public function __construct(mixed ...$args)
    {
        $this->args = $args;
    }
    public static function calculateMultiplier(mixed ...$args): mixed { return 1.0; }
    public static function canGrow(mixed ...$args): mixed { return true; }
    public static function hasEnoughLight(mixed ...$args): mixed { return true; }
}
