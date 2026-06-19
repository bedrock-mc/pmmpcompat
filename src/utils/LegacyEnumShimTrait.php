<?php

declare(strict_types=1);

namespace pocketmine\utils;

trait LegacyEnumShimTrait
{
    public static function __callStatic(string $name, array $arguments): self
    {
        if (count($arguments) > 0) {
            throw new \ArgumentCountError('Expected exactly 0 arguments, ' . count($arguments) . ' passed');
        }
        $key = strtoupper($name);
        return self::getAll()[$key] ?? throw new \InvalidArgumentException('Unknown enum case: ' . $name);
    }

    /** @return array<string, self> */
    public static function getAll(): array
    {
        $result = [];
        foreach (self::cases() as $case) {
            $result[strtoupper($case->name)] = $case;
        }
        return $result;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function id(): int
    {
        return spl_object_id($this);
    }

    public function equals(self $other): bool
    {
        return $this === $other;
    }
}
