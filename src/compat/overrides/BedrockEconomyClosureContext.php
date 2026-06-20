<?php

declare(strict_types=1);

namespace cooldogedev\BedrockEconomy\libs\cooldogedev\libSQL\context;

use Closure;
use Threaded;

class ClosureContext extends Threaded
{
    protected const CLOSURE_CONTEXT_KEY = 'CONTEXT';

    /** @var array<int, array{first: Closure|null, closures: Closure[], running: bool}> */
    private static array $contexts = [];
    private static int $nextContextId = 1;

    protected int $contextId;

    /** @param Closure[] $closures */
    public function __construct(array $closures = [])
    {
        $this->contextId = self::$nextContextId++;
        self::$contexts[$this->contextId] = ['first' => null, 'closures' => $closures, 'running' => false];
    }

    public static function create(...$closures): ClosureContext
    {
        return new ClosureContext($closures);
    }

    public function first(Closure $closure): ClosureContext
    {
        $context = &self::$contexts[$this->contextId];
        if ($context['first'] !== null) {
            $context['closures'][] = $context['first'];
        }
        $context['first'] = $closure;
        return $this;
    }

    public function getFirst(): ?Closure
    {
        return self::$contexts[$this->contextId]['first'] ?? null;
    }

    public function push(Closure $closure): ClosureContext
    {
        self::$contexts[$this->contextId]['closures'][] = $closure;
        return $this;
    }

    public function invoke(mixed $response, ?string $error): void
    {
        $this->setRunning(true);
        $first = $this->getFirst();
        if ($first !== null) {
            $newValue = $first($response, fn() => $this->setRunning(false), $error);
            if ($newValue !== null && $newValue !== $response) {
                $response = $newValue;
            }
        }
        foreach ($this->getClosures() as $closure) {
            if (!$this->isRunning()) {
                break;
            }
            $newValue = $closure($response, fn() => $this->setRunning(false), $error);
            if ($newValue !== null && $newValue !== $response) {
                $response = $newValue;
            }
        }
        $this->setRunning(false);
        self::$contexts[$this->contextId]['first'] = null;
    }

    /** @return Closure[] */
    public function getClosures(): array
    {
        return self::$contexts[$this->contextId]['closures'] ?? [];
    }

    public function isRunning(): bool
    {
        return self::$contexts[$this->contextId]['running'] ?? false;
    }

    public function setRunning(bool $running): void
    {
        self::$contexts[$this->contextId]['running'] = $running;
    }

    public function offsetExists(mixed $offset): bool
    {
        return $offset === self::CLOSURE_CONTEXT_KEY && self::$contexts[$this->contextId]['closures'] !== [];
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $offset === self::CLOSURE_CONTEXT_KEY ? self::$contexts[$this->contextId]['closures'] : null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset === self::CLOSURE_CONTEXT_KEY) {
            self::$contexts[$this->contextId]['closures'] = is_array($value) ? $value : [$value];
            return;
        }
        self::$contexts[$this->contextId]['closures'][] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        if ($offset === self::CLOSURE_CONTEXT_KEY) {
            self::$contexts[$this->contextId]['closures'] = [];
        }
    }
}
