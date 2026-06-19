<?php

declare(strict_types=1);

namespace pocketmine\scheduler;

class AsyncPoolWorkerEntry
{
    public int $lastUsed;
    public \SplQueue $tasks;

    public function __construct(
        public readonly AsyncWorker $worker,
        public readonly int $sleeperNotifierId = 0,
    ) {
        $this->lastUsed = time();
        $this->tasks = new \SplQueue();
    }

    public function submit(AsyncTask $task): void
    {
        $this->tasks->enqueue($task);
        $this->lastUsed = time();
        $this->worker->stack($task);
    }
}
