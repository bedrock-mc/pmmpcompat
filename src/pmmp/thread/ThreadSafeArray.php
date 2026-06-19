<?php

declare(strict_types=1);

namespace pmmp\thread;

class ThreadSafeArray extends ThreadSafe implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /** @var array<int|string, mixed> */
    private array $values = [];

    /** @param array<int|string, mixed> $values */
    public static function fromArray(array $values): self
    {
        $array = new self();
        $array->values = $values;
        return $array;
    }

    public function shift(): mixed
    {
        return array_shift($this->values);
    }

    public function count(): int
    {
        return count($this->values);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->values);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->values[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->values[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset === null) {
            $this->values[] = $value;
            return;
        }
        $this->values[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->values[$offset]);
    }

    /** @return array<int|string, mixed> */
    public function toArray(): array
    {
        return $this->values;
    }
}
