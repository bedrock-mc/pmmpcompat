<?php

declare(strict_types=1);

namespace pocketmine\thread;

trait CommonThreadPartsTrait
{
    private ?\pmmp\thread\ThreadSafeArray $classLoaders = null;
    private ?ThreadCrashInfo $crashInfo = null;
    protected ?string $composerAutoloaderPath = null;
    protected bool $isKilled = false;

    public function getClassLoaders(): ?array
    {
        if ($this->classLoaders === null) {
            return null;
        }
        return method_exists($this->classLoaders, 'toArray') ? $this->classLoaders->toArray() : iterator_to_array($this->classLoaders);
    }

    public function setClassLoaders(?array $autoloaders = null): void
    {
        $this->composerAutoloaderPath = defined('PMMPCOMPAT_AUTOLOADER_PATH') ? (string) \PMMPCOMPAT_AUTOLOADER_PATH : null;
        $this->classLoaders = \pmmp\thread\ThreadSafeArray::fromArray($autoloaders ?? [ThreadSafeClassLoader::getDefault()]);
    }

    public function registerClassLoaders(): void
    {
        if ($this->composerAutoloaderPath !== null) {
            require_once $this->composerAutoloaderPath;
        }
        ThreadSafeClassLoader::loadEnvironmentPaths();
        ThreadSafeClassLoader::getDefault()->register(false);
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
        try {
            if (method_exists($this, 'setRunning')) {
                $this->setRunning(true);
            }
            return parent::start($options) && $this->crashInfo === null;
        } catch (\Throwable $e) {
            $this->crashInfo = ThreadCrashInfo::fromThrowable($e, $this->getThreadName());
            return false;
        }
    }

    final public function run(): void
    {
        $this->registerClassLoaders();
        try {
            $this->onRun();
        } catch (\Throwable $e) {
            if (extension_loaded('pmmpthread')) {
                throw $e;
            }
            $this->crashInfo = ThreadCrashInfo::fromThrowable($e, $this->getThreadName());
        }
        $this->isKilled = true;
    }

    public function quit(): void
    {
        $this->isKilled = true;
        if (!$this->isJoined()) {
            $this->notify();
            $this->join();
        }
        ThreadManager::getInstance()->remove($this);
    }

    public function isStarted(): bool
    {
        return parent::isStarted();
    }

    public function isJoined(): bool
    {
        return parent::isJoined();
    }

    public function isTerminated(): bool
    {
        return $this->isKilled || $this->crashInfo !== null || parent::isTerminated();
    }

    public function join(): bool
    {
        $joined = parent::join();
        ThreadManager::getInstance()->remove($this);
        return $joined;
    }

    public function notify(): bool
    {
        return parent::notify();
    }

    public function synchronized(\Closure $closure, mixed ...$args): mixed
    {
        return parent::synchronized($closure, ...$args);
    }

    abstract protected function onRun(): void;

    public function getThreadName(): string
    {
        $class = static::class;
        return str_contains($class, '\\') ? substr($class, strrpos($class, '\\') + 1) : $class;
    }
}
