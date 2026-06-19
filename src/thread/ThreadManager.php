<?php

declare(strict_types=1);

namespace pocketmine\thread;

class ThreadManager
{
    private static ?self $instance = null;
    /** @var array<int, Worker|Thread> */
    private array $threads = [];

    public static function init(): void
    {
        self::$instance = new self();
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function add(Worker|Thread $thread): void
    {
        $this->threads[spl_object_id($thread)] = $thread;
    }

    public function remove(Worker|Thread $thread): void
    {
        unset($this->threads[spl_object_id($thread)]);
    }

    public function getAll(): array
    {
        return $this->threads;
    }

    public function stopAll(): int
    {
        $erroredThreads = 0;
        foreach ($this->getAll() as $thread) {
            try {
                $thread->quit();
            } catch (ThreadException) {
                $erroredThreads++;
            }
        }
        return $erroredThreads;
    }
}
