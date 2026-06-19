<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

class RailConnectionInfo
{
    public const CONNECTIONS = 0;
    public const CURVE_CONNECTIONS = 0;
    public const FLAG_ASCEND = 0;
    /** @var list<mixed> */
    private array $args;

    public function __construct(mixed ...$args)
    {
        $this->args = $args;
    }
}
