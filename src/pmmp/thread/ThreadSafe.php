<?php

declare(strict_types=1);

namespace pmmp\thread;

class ThreadSafe
{
    /** @var list<\Fiber> */
    private array $waiting = [];

    public function synchronized(\Closure $closure, mixed ...$args): mixed
    {
        return $closure(...$args);
    }

    public function wait(int $timeout = 0): bool
    {
        $fiber = \Fiber::getCurrent();
        if ($fiber === null) {
            return false;
        }
        $this->waiting[] = $fiber;
        \Fiber::suspend();
        return true;
    }

    public function notify(): bool
    {
        $notified = false;
        while (($fiber = array_shift($this->waiting)) !== null) {
            if ($fiber->isSuspended()) {
                $fiber->resume();
                $notified = true;
            }
        }
        return $notified;
    }

    public function notifyOne(): bool
    {
        while (($fiber = array_shift($this->waiting)) !== null) {
            if ($fiber->isSuspended()) {
                $fiber->resume();
                return true;
            }
        }
        return false;
    }
}
