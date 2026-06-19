<?php

declare(strict_types=1);

namespace pocketmine\scheduler;

abstract class Task
{
    private ?TaskHandler $handler = null;

    abstract public function onRun(): void;

    public function onCancel(): void {}

    final public function getHandler(): ?TaskHandler
    {
        return $this->handler;
    }

    public function getName(): string
    {
        $class = static::class;
        return str_contains($class, '\\') ? substr($class, strrpos($class, '\\') + 1) : $class;
    }

    final public function setHandler(?TaskHandler $handler): void
    {
        $this->handler = $handler;
    }
}
