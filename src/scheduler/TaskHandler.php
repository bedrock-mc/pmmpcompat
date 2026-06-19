<?php

declare(strict_types=1);

namespace pocketmine\scheduler;

class TaskHandler
{
    private bool $cancelled = false;

    public function __construct(
        private Task $task,
        private int $nextRun,
        private int $period = -1,
        private string $ownerName = 'Unknown',
        private int $delay = 0,
    ) {
        if ($task->getHandler() !== null) {
            throw new \InvalidArgumentException('Cannot assign multiple handlers to the same task');
        }
        $task->setHandler($this);
    }

    public function getTask(): Task
    {
        return $this->task;
    }

    public function getNextRun(): int
    {
        return $this->nextRun;
    }

    public function setNextRun(int $tick): void
    {
        $this->nextRun = $tick;
    }

    public function getPeriod(): int
    {
        return $this->period;
    }

    public function getDelay(): int
    {
        return $this->delay;
    }

    public function isDelayed(): bool
    {
        return $this->delay > 0;
    }

    public function getTaskName(): string
    {
        return $this->task->getName();
    }

    public function getOwnerName(): string
    {
        return $this->ownerName;
    }

    public function isRepeating(): bool
    {
        return $this->period > 0;
    }

    public function isCancelled(): bool
    {
        return $this->cancelled;
    }

    public function cancel(): void
    {
        if (!$this->cancelled) {
            $this->cancelled = true;
            $this->task->onCancel();
            $this->task->setHandler(null);
        }
    }

    public function remove(): void
    {
        $this->cancelled = true;
        $this->task->setHandler(null);
    }

    public function run(): void
    {
        try {
            $this->task->onRun();
        } catch (CancelTaskException) {
            $this->cancel();
        }
    }
}
