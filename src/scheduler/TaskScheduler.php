<?php

declare(strict_types=1);

namespace pocketmine\scheduler;

class TaskScheduler
{
    /** @var TaskHandler[] */
    private array $tasks = [];
    private int $currentTick = 0;
    private bool $enabled = true;

    public function __construct(private string $ownerName) {}

    public function scheduleTask(Task $task): TaskHandler
    {
        return $this->scheduleDelayedTask($task, 0);
    }

    public function scheduleDelayedTask(Task $task, int $delay): TaskHandler
    {
        return $this->addTask($task, max(0, $delay), -1);
    }

    public function scheduleRepeatingTask(Task $task, int $period): TaskHandler
    {
        return $this->addTask($task, max(1, $period), max(1, $period));
    }

    public function scheduleDelayedRepeatingTask(Task $task, int $delay, int $period): TaskHandler
    {
        return $this->addTask($task, max(0, $delay), max(1, $period));
    }

    public function cancelAllTasks(): void
    {
        foreach ($this->tasks as $task) {
            $task->cancel();
        }
        $this->tasks = [];
    }

    public function isQueued(Task $task): bool
    {
        foreach ($this->tasks as $handler) {
            if ($handler->getTask() === $task && !$handler->isCancelled()) {
                return true;
            }
        }
        return false;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function shutdown(): void
    {
        $this->enabled = false;
        $this->cancelAllTasks();
    }

    public function mainThreadHeartbeat(int $currentTick): void
    {
        $this->currentTick = $currentTick;
        if (!$this->enabled) {
            return;
        }
        foreach ($this->tasks as $id => $handler) {
            if ($handler->isCancelled()) {
                unset($this->tasks[$id]);
                continue;
            }
            if ($handler->getNextRun() > $currentTick) {
                continue;
            }
            $handler->run();
            if ($handler->isRepeating() && !$handler->isCancelled()) {
                $handler->setNextRun($currentTick + $handler->getPeriod());
            } else {
                $handler->cancel();
                unset($this->tasks[$id]);
            }
        }
    }

    private function addTask(Task $task, int $delay, int $period): TaskHandler
    {
        $handler = new TaskHandler($task, $this->currentTick + $delay, $period, $this->ownerName, $delay);
        $this->tasks[spl_object_id($handler)] = $handler;
        return $handler;
    }
}
