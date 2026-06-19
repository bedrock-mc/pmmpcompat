<?php

declare(strict_types=1);

namespace pocketmine\snooze;

final class SleeperNotifier extends \pmmp\thread\ThreadSafe
{
    public function __construct(private SleeperNotifierState $state, private int $id) {}

    public function wakeupSleeper(): void
    {
        $this->state->pending[$this->id] = true;
    }
}
