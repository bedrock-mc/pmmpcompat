<?php

declare(strict_types=1);

namespace pocketmine\nbt\tag;

use pocketmine\nbt\NbtDataException;

class CompoundTag extends Tag implements \Countable, \IteratorAggregate
{
    /** @var array<string, Tag> */
    private array $value;

    /**
     * @param array<string, Tag> $value
     */
    public function __construct(array $value = [])
    {
        $this->value = [];
        foreach ($value as $name => $tag) {
            $this->setTag((string) $name, $tag);
        }
    }

    public static function create(): self
    {
        return new self();
    }

    /**
     * @return array<string, Tag>
     */
    public function getValue(): array
    {
        return $this->value;
    }

    public function getTag(string $name): ?Tag
    {
        return $this->value[$name] ?? null;
    }

    public function setTag(string $name, Tag $tag): self
    {
        $this->value[$name] = $tag;
        return $this;
    }

    public function removeTag(string $name): self
    {
        unset($this->value[$name]);
        return $this;
    }

    public function getCompoundTag(string $name): ?self
    {
        $tag = $this->getTag($name);
        return $tag instanceof self ? $tag : null;
    }

    public function setByte(string $name, int $value): self { return $this->setTag($name, new ByteTag($value)); }
    public function setShort(string $name, int $value): self { return $this->setTag($name, new ShortTag($value)); }
    public function setInt(string $name, int $value): self { return $this->setTag($name, new IntTag($value)); }
    public function setLong(string $name, int $value): self { return $this->setTag($name, new LongTag($value)); }
    public function setFloat(string $name, float $value): self { return $this->setTag($name, new FloatTag($value)); }
    public function setDouble(string $name, float $value): self { return $this->setTag($name, new DoubleTag($value)); }
    public function setString(string $name, string $value): self { return $this->setTag($name, new StringTag($value)); }
    public function setByteArray(string $name, string $value): self { return $this->setTag($name, new ByteArrayTag($value)); }
    /** @param list<int> $value */
    public function setIntArray(string $name, array $value): self { return $this->setTag($name, new IntArrayTag($value)); }

    public function getByte(string $name, int $default = 0): int { return $this->readInt($name, $default); }
    public function getShort(string $name, int $default = 0): int { return $this->readInt($name, $default); }
    public function getInt(string $name, int $default = 0): int { return $this->readInt($name, $default); }
    public function getLong(string $name, int $default = 0): int { return $this->readInt($name, $default); }
    public function getFloat(string $name, float $default = 0.0): float { return $this->readFloat($name, $default); }
    public function getDouble(string $name, float $default = 0.0): float { return $this->readFloat($name, $default); }

    public function getString(string $name, string $default = ''): string
    {
        $tag = $this->getTag($name);
        return $tag instanceof StringTag ? $tag->getValue() : $default;
    }

    public function getByteArray(string $name, string $default = ''): string
    {
        $tag = $this->getTag($name);
        return $tag instanceof ByteArrayTag ? $tag->getValue() : $default;
    }

    /**
     * @return list<int>
     */
    public function getIntArray(string $name, array $default = []): array
    {
        $tag = $this->getTag($name);
        return $tag instanceof IntArrayTag ? $tag->getValue() : $default;
    }

    public function count(): int
    {
        return count($this->value);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->value);
    }

    public function toCompatibilityData(): array
    {
        $value = [];
        foreach ($this->value as $name => $tag) {
            $value[$name] = $tag->toCompatibilityData();
        }
        return ['type' => static::class, 'value' => $value];
    }

    public static function fromCompatibilityData(array $data): Tag
    {
        $type = $data['type'] ?? null;
        $value = $data['value'] ?? null;

        return match ($type) {
            self::class => new self(self::decodeCompoundValue($value)),
            ByteTag::class => new ByteTag((int) $value),
            ShortTag::class => new ShortTag((int) $value),
            IntTag::class => new IntTag((int) $value),
            LongTag::class => new LongTag((int) $value),
            FloatTag::class => new FloatTag((float) $value),
            DoubleTag::class => new DoubleTag((float) $value),
            StringTag::class => new StringTag((string) $value),
            ByteArrayTag::class => new ByteArrayTag((string) $value),
            IntArrayTag::class => new IntArrayTag(is_array($value) ? $value : []),
            ListTag::class => new ListTag(self::decodeListValue($value), isset($data['tagType']) ? (int) $data['tagType'] : null),
            default => throw new NbtDataException('Unknown compatibility NBT tag type'),
        };
    }

    private function readInt(string $name, int $default): int
    {
        $tag = $this->getTag($name);
        return $tag instanceof ScalarTag && is_numeric($tag->getValue()) ? (int) $tag->getValue() : $default;
    }

    private function readFloat(string $name, float $default): float
    {
        $tag = $this->getTag($name);
        return $tag instanceof ScalarTag && is_numeric($tag->getValue()) ? (float) $tag->getValue() : $default;
    }

    /**
     * @return array<string, Tag>
     */
    private static function decodeCompoundValue(mixed $value): array
    {
        if (!is_array($value)) {
            throw new NbtDataException('Invalid compatibility compound tag value');
        }

        $decoded = [];
        foreach ($value as $name => $tagData) {
            if (!is_array($tagData)) {
                throw new NbtDataException('Invalid compatibility tag payload');
            }
            $decoded[(string) $name] = self::fromCompatibilityData($tagData);
        }
        return $decoded;
    }

    /**
     * @return list<Tag>
     */
    private static function decodeListValue(mixed $value): array
    {
        if (!is_array($value)) {
            throw new NbtDataException('Invalid compatibility list tag value');
        }

        $decoded = [];
        foreach ($value as $tagData) {
            if (!is_array($tagData)) {
                throw new NbtDataException('Invalid compatibility list tag payload');
            }
            $decoded[] = self::fromCompatibilityData($tagData);
        }
        return $decoded;
    }
}
