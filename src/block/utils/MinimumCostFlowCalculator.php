<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

class MinimumCostFlowCalculator
{
    /** @var list<mixed> */
    private array $args;

    public function __construct(mixed ...$args)
    {
        $this->args = $args;
    }
    public function getOptimalFlowDirections(mixed ...$args): mixed { return [2, 3, 4, 5]; }
}
