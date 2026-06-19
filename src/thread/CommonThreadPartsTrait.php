<?php

declare(strict_types=1);

namespace pocketmine\thread;

trait CommonThreadPartsTrait
{
    /** @var ThreadSafeClassLoader[]|null */
    private ?array $classLoaders = null;
    private ?ThreadCrashInfo $crashInfo = null;
    private bool $started = false;
    private bool $joined = false;
    protected bool $isKilled = false;

    public function getClassLoaders(): ?array
    {
        return $this->classLoaders;
    }

    public function setClassLoaders(?array $autoloaders = null): void
    {
        $this->classLoaders = $autoloaders ?? [];
    }

    public function registerClassLoaders(): void
    {
        foreach ($this->classLoaders ?? [] as $autoloader) {
            $autoloader->register(false);
        }
    }

    public function getCrashInfo(): ?ThreadCrashInfo
    {
        return $this->crashInfo;
    }

    public function start(int $options = 0): bool
    {
        ThreadManager::getInstance()->add($this);
        if ($this->classLoaders === null) {
            $this->setClassLoaders();
        }
        $this->started = true;
        try {
            $this->run();
            return true;
        } catch (\Throwable $e) {
            $this->crashInfo = ThreadCrashInfo::fromThrowable($e, $this->getThreadName());
            return false;
        }
    }

    final public function run(): void
    {
        $this->registerClassLoaders();
        $this->onRun();
        $this->isKilled = true;
        $this->joined = true;
    }

    public function quit(): void
    {
        $this->isKilled = true;
        $this->joined = true;
        ThreadManager::getInstance()->remove($this);
    }

    public function isStarted(): bool
    {
        return $this->started;
    }

    public function isJoined(): bool
    {
        return $this->joined;
    }

    public function isTerminated(): bool
    {
        return $this->isKilled || $this->crashInfo !== null;
    }

    public function join(): bool
    {
        $this->joined = true;
        ThreadManager::getInstance()->remove($this);
        return true;
    }

    public function notify(): void
    {
    }

    public function synchronized(\Closure $closure, mixed ...$args): mixed
    {
        return $closure(...$args);
    }

    abstract protected function onRun(): void;

    public function getThreadName(): string
    {
        $class = static::class;
        return str_contains($class, '\\') ? substr($class, strrpos($class, '\\') + 1) : $class;
    }
}
