<?php

declare(strict_types=1);

namespace pocketmine\scheduler;

class AsyncPool
{
    /** @var array<int, AsyncPoolWorkerEntry> */
    private array $workers = [];
    /** @var array<int, \Closure> */
    private array $workerStartHooks = [];
    private int $size;

    public function __construct(int $size = 1, mixed ...$args)
    {
        $this->size = max(1, $size);
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function increaseSize(int $newSize): void
    {
        if ($newSize > $this->size) {
            $this->size = $newSize;
        }
    }

    public function addWorkerStartHook(\Closure $hook): void
    {
        $this->workerStartHooks[spl_object_id($hook)] = $hook;
        foreach (array_keys($this->workers) as $workerId) {
            $hook($workerId);
        }
    }

    public function removeWorkerStartHook(\Closure $hook): void
    {
        unset($this->workerStartHooks[spl_object_id($hook)]);
    }

    /** @return list<int> */
    public function getRunningWorkers(): array
    {
        return array_keys($this->workers);
    }

    public function submitTaskToWorker(AsyncTask $task, int $worker): void
    {
        if ($worker < 0 || $worker >= $this->size) {
            throw new \InvalidArgumentException("Invalid worker $worker");
        }
        if ($task->isSubmitted()) {
            throw new \InvalidArgumentException('Cannot submit the same AsyncTask instance more than once');
        }
        $task->setSubmitted();
        $this->getWorker($worker)->submit($task);
    }

    public function selectWorker(): int
    {
        $worker = null;
        $minUsage = PHP_INT_MAX;
        foreach ($this->workers as $workerId => $entry) {
            $usage = $entry->tasks->count();
            if ($usage < $minUsage) {
                $worker = $workerId;
                $minUsage = $usage;
            }
        }
        if ($worker === null || ($minUsage > 0 && count($this->workers) < $this->size)) {
            for ($i = 0; $i < $this->size; $i++) {
                if (!isset($this->workers[$i])) {
                    return $i;
                }
            }
        }
        return $worker ?? 0;
    }

    public function submitTask(AsyncTask $task): int
    {
        $worker = $this->selectWorker();
        $this->submitTaskToWorker($task, $worker);
        return $worker;
    }

    public function collectTasks(): bool
    {
        $more = false;
        foreach (array_keys($this->workers) as $workerId) {
            $more = $this->collectTasksFromWorker($workerId) || $more;
        }
        return $more;
    }

    public function collectTasksFromWorker(int $worker): bool
    {
        if (!isset($this->workers[$worker])) {
            throw new \InvalidArgumentException("No such worker $worker");
        }
        $queue = $this->workers[$worker]->tasks;
        while (!$queue->isEmpty()) {
            /** @var AsyncTask $task */
            $task = $queue->bottom();
            if (!$task->isFinished()) {
                $task->run();
            }
            $queue->dequeue();
            $task->checkProgressUpdates();
            if (!$task->isCrashed()) {
                $task->onCompletion();
            }
        }
        return false;
    }

    /** @return array<int, int> */
    public function getTaskQueueSizes(): array
    {
        $sizes = [];
        foreach ($this->workers as $workerId => $entry) {
            $sizes[$workerId] = $entry->tasks->count();
        }
        return $sizes;
    }

    public function shutdownUnusedWorkers(): int
    {
        $removed = 0;
        foreach ($this->workers as $workerId => $entry) {
            if ($entry->tasks->isEmpty()) {
                unset($this->workers[$workerId]);
                $removed++;
            }
        }
        return $removed;
    }

    public function shutdown(): void
    {
        $this->collectTasks();
        $this->workers = [];
    }

    private function getWorker(int $workerId): AsyncPoolWorkerEntry
    {
        if (!isset($this->workers[$workerId])) {
            $this->workers[$workerId] = new AsyncPoolWorkerEntry(new AsyncWorker($workerId), $workerId);
            foreach ($this->workerStartHooks as $hook) {
                $hook($workerId);
            }
        }
        return $this->workers[$workerId];
    }
}
