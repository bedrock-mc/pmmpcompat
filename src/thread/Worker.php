<?php

declare(strict_types=1);

namespace pocketmine\thread;

abstract class Worker
{
    use CommonThreadPartsTrait;

    /** @var list<object> */
    private array $stack = [];

    public function stack(object $work): void
    {
        $this->stack[] = $work;
    }

    public function collect(): int
    {
        $count = count($this->stack);
        $this->stack = [];
        return $count;
    }
}
