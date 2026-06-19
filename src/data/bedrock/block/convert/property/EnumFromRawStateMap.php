<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block\convert\property;

class EnumFromRawStateMap implements StateMap
{
    /** @var array<int, int|string> */
    private array $enumToValue = [];
    /** @var array<int|string, \UnitEnum> */
    private array $valueToEnum = [];

    public function __construct(string $class, \Closure $mapper, ?\Closure $aliasMapper = null)
    {
        foreach ($class::cases() as $case) {
            $raw = $mapper($case);
            $this->valueToEnum[$raw] = $case;
            $this->enumToValue[spl_object_id($case)] = $raw;
            if ($aliasMapper !== null) {
                foreach ($aliasMapper($case) as $alias) {
                    $this->valueToEnum[$alias] = $case;
                }
            }
        }
    }

    public function getRawToValueMap(): array { return $this->valueToEnum; }
    public static function int(string $class, \Closure $mapper, ?\Closure $aliasMapper = null): self { return new self($class, $mapper, $aliasMapper); }
    public function printableValue(mixed $value): string { return $value instanceof \UnitEnum ? $value::class . '::' . $value->name : (string) $value; }
    public function rawToValue(int|string $raw): ?\UnitEnum { return $this->valueToEnum[$raw] ?? null; }
    public static function string(string $class, \Closure $mapper, ?\Closure $aliasMapper = null): self { return new self($class, $mapper, $aliasMapper); }
    public function valueToRaw(mixed $value): int|string
    {
        if (!$value instanceof \UnitEnum) {
            throw new \InvalidArgumentException('Expected enum value');
        }
        return $this->enumToValue[spl_object_id($value)] ?? throw new \InvalidArgumentException('No raw state mapping for ' . $this->printableValue($value));
    }
}
