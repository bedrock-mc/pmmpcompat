<?php

declare(strict_types=1);

namespace pocketmine\snooze;

final class SleeperNotifier extends \pmmp\thread\ThreadSafe
{
    public function __construct(private ?SleeperNotifierState $state = null, private int $id = 0) {}

    public function bind(SleeperNotifierState $state, int $id): void
    {
        $this->state = $state;
        $this->id = $id;
    }

    public function wakeupSleeper(): void
    {
        if ($this->state === null) {
            return;
        }
        $this->state->pending[$this->id] = true;
    }
}
