<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

class FortuneDropHelper
{
    /** @var list<mixed> */
    private array $args;

    public function __construct(mixed ...$args)
    {
        $this->args = $args;
    }
    public static function binomial(mixed ...$args): mixed { return max((int) ($args[1] ?? 0), min((int) ($args[2] ?? $args[1] ?? 0), (int) ($args[1] ?? 0))); }
    public static function bonusChanceDivisor(mixed ...$args): mixed { return true; }
    public static function bonusChanceFixed(mixed ...$args): mixed { return true; }
    public static function discrete(mixed ...$args): mixed { return max((int) ($args[1] ?? 0), min((int) ($args[2] ?? $args[1] ?? 0), (int) ($args[1] ?? 0))); }
    public static function weighted(mixed ...$args): mixed { return max((int) ($args[1] ?? 0), min((int) ($args[2] ?? $args[1] ?? 0), (int) ($args[1] ?? 0))); }
}
