<?php

declare(strict_types=1);

namespace pocketmine\scheduler;

class ClosureTask extends Task
{
    /** @param \Closure(): void $closure */
    public function __construct(private \Closure $closure) {}

    public function onRun(): void
    {
        ($this->closure)();
    }

    public function getName(): string
    {
        return parent::getName();
    }
}
