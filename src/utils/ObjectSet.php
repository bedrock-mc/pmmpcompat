<?php

declare(strict_types=1);

namespace pocketmine\utils;

final class ObjectSet implements \IteratorAggregate
{
    /** @var array<int, object> */
    private array $objects = [];

    public function add(object ...$objects): void
    {
        foreach ($objects as $object) {
            $this->objects[spl_object_id($object)] = $object;
        }
    }

    public function remove(object ...$objects): void
    {
        foreach ($objects as $object) {
            unset($this->objects[spl_object_id($object)]);
        }
    }

    public function clear(): void
    {
        $this->objects = [];
    }

    public function contains(object $object): bool
    {
        return array_key_exists(spl_object_id($object), $this->objects);
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->objects);
    }

    /** @return array<int, object> */
    public function toArray(): array
    {
        return $this->objects;
    }
}
