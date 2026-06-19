<?php

declare(strict_types=1);

namespace pocketmine\scheduler;

class AsyncWorker
{
    /** @var array<string, mixed> */
    private array $threadStore = [];
    private int $workerId;

    public function __construct(mixed $loggerOrWorkerId = 0, ?int $workerId = null, mixed ...$args)
    {
        $this->workerId = $workerId ?? (is_int($loggerOrWorkerId) ? $loggerOrWorkerId : 0);
    }

    public function getAsyncWorkerId(): int
    {
        return $this->workerId;
    }

    public function getFromThreadStore(string $identifier): mixed
    {
        return $this->threadStore[$identifier] ?? null;
    }

    public function getLogger(): mixed
    {
        return null;
    }

    public static function getNotifier(): object
    {
        return new class {
            public function wakeupSleeper(): void
            {
            }
        };
    }

    public function getThreadName(): string
    {
        return 'AsyncWorker#' . $this->workerId;
    }

    public static function maybeCollectCycles(): void
    {
        gc_collect_cycles();
    }

    public function removeFromThreadStore(string $identifier): void
    {
        unset($this->threadStore[$identifier]);
    }

    public function saveToThreadStore(string $identifier, mixed $value): void
    {
        $this->threadStore[$identifier] = $value;
    }

    public function stack(AsyncTask $task): void
    {
    }
}
