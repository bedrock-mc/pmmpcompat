<?php

declare(strict_types=1);

namespace pocketmine\event;

final class EventPriority
{
    public const LOWEST = 5;
    public const LOW = 4;
    public const NORMAL = 3;
    public const HIGH = 2;
    public const HIGHEST = 1;
    public const MONITOR = 0;
    public const ALL = [
        self::LOWEST,
        self::LOW,
        self::NORMAL,
        self::HIGH,
        self::HIGHEST,
        self::MONITOR,
    ];

    private function __construct()
    {
    }

    public static function fromString(string $name): int
    {
        return match (strtoupper($name)) {
            'LOWEST' => self::LOWEST,
            'LOW' => self::LOW,
            'NORMAL' => self::NORMAL,
            'HIGH' => self::HIGH,
            'HIGHEST' => self::HIGHEST,
            'MONITOR' => self::MONITOR,
            default => throw new \InvalidArgumentException('Unable to resolve priority "' . $name . '"'),
        };
    }
}
