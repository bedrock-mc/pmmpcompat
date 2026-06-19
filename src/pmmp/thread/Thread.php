<?php

declare(strict_types=1);

namespace pmmp\thread;

abstract class Thread extends ThreadSafe
{
    public const INHERIT_NONE = 0;
    public const INHERIT_INI = 1;
    public const INHERIT_CONSTANTS = 16;
    public const INHERIT_FUNCTIONS = 256;
    public const INHERIT_CLASSES = 4096;
    public const INHERIT_INCLUDES = 65536;
    public const INHERIT_COMMENTS = 1048576;
    public const INHERIT_ALL = 1118481;
    public const ALLOW_HEADERS = 268435456;

    private ?\Fiber $fiber = null;
    private bool $started = false;
    private bool $joined = false;
    private bool $terminated = false;

    public static function getCurrentThread(): ?self
    {
        return null;
    }

    public static function getCurrentThreadId(): int
    {
        return 0;
    }

    public static function getRunningCount(): int
    {
        return 0;
    }

    public function getCreatorId(): int
    {
        return 0;
    }

    public function getSharedGlobals(): ThreadSafeArray
    {
        return new ThreadSafeArray();
    }

    public function getThreadId(): int
    {
        return spl_object_id($this);
    }

    public function isJoined(): bool
    {
        return $this->joined;
    }

    public function isStarted(): bool
    {
        return $this->started;
    }

    public function isTerminated(): bool
    {
        return $this->terminated;
    }

    public function start(int $options = self::INHERIT_NONE): bool
    {
        $this->started = true;
        $this->fiber = new \Fiber(function (): void {
            $this->run();
            $this->terminated = true;
        });
        $this->fiber->start();
        return true;
    }

    public function join(): bool
    {
        while ($this->fiber !== null && $this->fiber->isSuspended()) {
            $this->fiber->resume();
        }
        $this->joined = true;
        return true;
    }

    abstract public function run(): void;
}
