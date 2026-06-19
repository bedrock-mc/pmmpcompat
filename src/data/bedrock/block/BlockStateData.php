<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block;

use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\Tag;

class BlockStateData
{
    public const CURRENT_VERSION = 18168865;
    public const TAG_NAME = 'name';
    public const TAG_STATES = 'states';
    public const TAG_VERSION = 'version';

    /** @param array<string, Tag|bool|int|string> $states */
    public function __construct(private string $name, private array $states = [], private int $version = self::CURRENT_VERSION)
    {
        foreach ($this->states as $key => $state) {
            $this->states[$key] = self::normaliseTag($state);
        }
    }

    /** @param array<string, Tag|bool|int|string> $states */
    public static function current(string $name, array $states = []): self
    {
        return new self($name, $states, self::CURRENT_VERSION);
    }

    public function equals(self $other): bool
    {
        return $this->name === $other->name && $this->version === $other->version && $this->stateValues() === $other->stateValues();
    }

    public static function fromNbt(mixed $tag): self
    {
        if ($tag instanceof CompoundTag) {
            $value = $tag->getValue();
        } elseif (is_array($tag)) {
            $value = $tag;
        } else {
            return new self('minecraft:air');
        }
        return new self(
            (string) ($value[self::TAG_NAME] ?? 'minecraft:air'),
            is_array($value[self::TAG_STATES] ?? null) ? $value[self::TAG_STATES] : [],
            (int) ($value[self::TAG_VERSION] ?? self::CURRENT_VERSION)
        );
    }

    public function getName(): string { return $this->name; }
    public function getState(string $name): ?Tag { return $this->states[$name] ?? null; }
    /** @return array<string, Tag> */
    public function getStates(): array { return $this->states; }
    public function getVersion(): int { return $this->version; }
    public function getVersionAsString(): string
    {
        return (($this->version >> 24) & 0xff) . '.' . (($this->version >> 16) & 0xff) . '.' . (($this->version >> 8) & 0xff) . '.' . ($this->version & 0xff);
    }

    /** @return array{name: string, states: array<string, mixed>, version: int} */
    public function toNbt(): array
    {
        return [self::TAG_NAME => $this->name, self::TAG_STATES => $this->stateValues(), self::TAG_VERSION => $this->version];
    }

    /** @return array{name: string, states: array<string, mixed>, version: int} */
    public function toVanillaNbt(): array { return $this->toNbt(); }

    private static function normaliseTag(Tag|bool|int|string $value): Tag
    {
        return match (true) {
            $value instanceof Tag => $value,
            is_bool($value) => new ByteTag($value ? 1 : 0),
            is_int($value) => new IntTag($value),
            default => new StringTag((string) $value),
        };
    }

    /** @return array<string, mixed> */
    private function stateValues(): array
    {
        $values = [];
        foreach ($this->states as $name => $tag) {
            $values[$name] = $tag->getValue();
        }
        ksort($values);
        return $values;
    }
}
