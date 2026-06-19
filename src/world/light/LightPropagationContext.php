<?php

declare(strict_types=1);

namespace pocketmine\world\light;

class LightPropagationContext
{
    /** @var \SplQueue<array<int, int>> */
    public \SplQueue $removalQueue;
    /** @var \SplQueue<array<int, int>> */
    public \SplQueue $spreadQueue;
    /** @var array<int|string, bool> */
    public array $removalVisited = [];
    /** @var array<int|string, bool|int> */
    public array $spreadVisited = [];

    public function __construct()
    {
        $this->removalQueue = new \SplQueue();
        $this->spreadQueue = new \SplQueue();
    }
}
