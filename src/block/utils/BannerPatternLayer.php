<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

class BannerPatternLayer
{
    /** @var list<mixed> */
    private array $args;

    public function __construct(mixed ...$args)
    {
        $this->args = $args;
    }
    public function getColor(mixed ...$args): mixed { return $this->args[1] ?? $this->args[0] ?? null; }
    public function getType(mixed ...$args): mixed { return $this->args[0] ?? null; }
}
