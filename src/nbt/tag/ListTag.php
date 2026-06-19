<?php

declare(strict_types=1);

namespace pocketmine\nbt\tag;

class ListTag extends Tag implements \Countable, \IteratorAggregate, \ArrayAccess
{
    /** @var list<Tag> */
    private array $value;

    /**
     * @param list<Tag> $value
     */
    public function __construct(array $value = [], private ?int $tagType = null)
    {
        $this->value = [];
        foreach ($value as $tag) {
            $this->push($tag);
        }
    }

    public function push(Tag $tag): void
    {
        $this->value[] = $tag;
    }

    /**
     * @return list<Tag>
     */
    public function getValue(): array
    {
        return $this->value;
    }

    public function count(): int
    {
        return count($this->value);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->value);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->value[$offset]);
    }

    public function offsetGet(mixed $offset): ?Tag
    {
        return $this->value[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!$value instanceof Tag) {
            throw new \TypeError('ListTag values must be NBT tags');
        }
        if ($offset === null) {
            $this->value[] = $value;
        } else {
            $this->value[$offset] = $value;
            ksort($this->value);
            $this->value = array_values($this->value);
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->value[$offset]);
        $this->value = array_values($this->value);
    }

    public function toCompatibilityData(): array
    {
        return [
            'type' => static::class,
            'tagType' => $this->tagType,
            'value' => array_map(static fn(Tag $tag): array => $tag->toCompatibilityData(), $this->value),
        ];
    }
}
