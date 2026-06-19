<?php

declare(strict_types=1);

namespace pocketmine\entity\utils;

use pocketmine\utils\AssumptionFailedError;
use function max;
use function sqrt;

abstract class ExperienceUtils
{
    /**
     * Calculates and returns the amount of XP needed to get from level 0 to level $level.
     */
    public static function getXpToReachLevel(int $level): int
    {
        if ($level <= 16) {
            return $level ** 2 + $level * 6;
        }
        if ($level <= 31) {
            return (int) ($level ** 2 * 2.5 - 40.5 * $level + 360);
        }

        return (int) ($level ** 2 * 4.5 - 162.5 * $level + 2220);
    }

    /**
     * Returns the amount of XP needed to reach $level + 1.
     */
    public static function getXpToCompleteLevel(int $level): int
    {
        if ($level <= 15) {
            return 2 * $level + 7;
        }
        if ($level <= 30) {
            return 5 * $level - 38;
        }

        return 9 * $level - 158;
    }

    /**
     * Calculates and returns the number of XP levels the specified amount of XP points are worth.
     * This returns a floating-point number, the decimal part being the progress through the resulting level.
     */
    public static function getLevelFromXp(int $xp): float
    {
        if ($xp < 0) {
            throw new \InvalidArgumentException("XP must be at least 0");
        }
        if ($xp <= self::getXpToReachLevel(16)) {
            return self::solvePositiveQuadratic(1.0, 6.0, -$xp);
        }
        if ($xp <= self::getXpToReachLevel(31)) {
            return self::solvePositiveQuadratic(2.5, -40.5, 360.0 - $xp);
        }

        return self::solvePositiveQuadratic(4.5, -162.5, 2220.0 - $xp);
    }

    private static function solvePositiveQuadratic(float $a, float $b, float $c): float
    {
        $discriminant = $b ** 2 - 4 * $a * $c;
        if ($discriminant < 0) {
            throw new AssumptionFailedError("Expected at least 1 solution");
        }

        $root = sqrt($discriminant);
        return max((-$b + $root) / (2 * $a), (-$b - $root) / (2 * $a));
    }
}
