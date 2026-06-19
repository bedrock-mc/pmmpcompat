<?php

declare(strict_types=1);

namespace pocketmine\scheduler;

abstract class AsyncTask
{
    /** @var array<int, array<string, mixed>> */
    private static array $threadLocalStorage = [];

    /** @var list<mixed> */
    private array $progressUpdates = [];
    private mixed $result = null;
    private bool $submitted = false;
    private bool $finished = false;
    private bool $crashed = false;
    private bool $cancelledRun = false;

    final public function run(): void
    {
        $this->result = null;
        try {
            $this->onRun();
        } catch (\Throwable $e) {
            $this->crashed = true;
            $this->onError();
            throw $e;
        } finally {
            $this->finished = true;
            AsyncWorker::maybeCollectCycles();
        }
    }

    public function isCrashed(): bool
    {
        return $this->crashed;
    }

    public function isFinished(): bool
    {
        return $this->finished;
    }

    public function hasResult(): bool
    {
        return $this->result !== null;
    }

    public function getResult(): mixed
    {
        return $this->result;
    }

    public function setResult(mixed $result): void
    {
        $this->result = $result;
    }

    public function cancelRun(): void
    {
        $this->cancelledRun = true;
    }

    public function hasCancelledRun(): bool
    {
        return $this->cancelledRun;
    }

    final public function setSubmitted(): void
    {
        $this->submitted = true;
    }

    final public function isSubmitted(): bool
    {
        return $this->submitted;
    }

    abstract public function onRun(): void;

    public function onCompletion(): void
    {
    }

    public function publishProgress(mixed $progress): void
    {
        $this->progressUpdates[] = unserialize(serialize($progress));
    }

    public function checkProgressUpdates(): void
    {
        while ($this->progressUpdates !== []) {
            $progress = array_shift($this->progressUpdates);
            $this->onProgressUpdate($progress);
        }
    }

    public function onProgressUpdate(mixed $progress): void
    {
    }

    public function onError(): void
    {
    }

    protected function storeLocal(string $key, mixed $complexData): void
    {
        self::$threadLocalStorage[spl_object_id($this)][$key] = $complexData;
    }

    protected function fetchLocal(string $key): mixed
    {
        $id = spl_object_id($this);
        if (!isset(self::$threadLocalStorage[$id]) || !array_key_exists($key, self::$threadLocalStorage[$id])) {
            throw new \InvalidArgumentException('No matching thread-local data found on this thread');
        }
        return self::$threadLocalStorage[$id][$key];
    }

    final public function __destruct()
    {
        $this->reallyDestruct();
        unset(self::$threadLocalStorage[spl_object_id($this)]);
    }

    protected function reallyDestruct(): void
    {
    }
}
