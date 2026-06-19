<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

class BlockEventHelper
{
    /** @var list<mixed> */
    private array $args;

    public function __construct(mixed ...$args)
    {
        $this->args = $args;
    }
    public static function Block(mixed ...$args): mixed { return true; }
    public static function form(mixed ...$args): mixed { return true; }
    public static function grow(mixed ...$args): mixed { return true; }
    public static function melt(mixed ...$args): mixed { return true; }
    public static function spread(mixed ...$args): mixed { return true; }
}
