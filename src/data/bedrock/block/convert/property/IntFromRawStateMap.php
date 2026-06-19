<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block\convert\property;

class IntFromRawStateMap implements StateMap
{
    /** @var array<int, int|string> */
    private array $serializeMap;
    /** @var array<int|string, int> */
    private array $deserializeMap;

    /** @param array<int, int|string> $serializeMap @param array<int, int|string|array<int, int|string>> $deserializeAliases */
    public function __construct(array $serializeMap, array $deserializeAliases = [])
    {
        $this->serializeMap = $serializeMap;
        $this->deserializeMap = array_flip($serializeMap);
        foreach ($deserializeAliases as $value => $aliases) {
            foreach ((array) $aliases as $alias) {
                $this->deserializeMap[$alias] = (int) $value;
            }
        }
    }

    public function getRawToValueMap(): array { return $this->deserializeMap; }
    public static function int(array $serializeMap, array $deserializeAliases = []): self { return new self($serializeMap, $deserializeAliases); }
    public function printableValue(mixed $value): string { return (string) $value; }
    public function rawToValue(int|string $raw): ?int { return $this->deserializeMap[$raw] ?? null; }
    public static function string(array $serializeMap, array $deserializeAliases = []): self { return new self($serializeMap, $deserializeAliases); }
    public function valueToRaw(mixed $value): int|string
    {
        if (!array_key_exists((int) $value, $this->serializeMap)) {
            throw new \InvalidArgumentException('No raw state mapping for value ' . (string) $value);
        }
        return $this->serializeMap[(int) $value];
    }
}
