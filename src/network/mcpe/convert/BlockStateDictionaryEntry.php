<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\convert;

use pocketmine\data\bedrock\block\BlockStateData;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\Tag;

final class BlockStateDictionaryEntry
{
    /** @param array<string, Tag|bool|int|string> $stateProperties */
    public function __construct(private string $stateName, private array $stateProperties = [], private int $meta = 0)
    {
        foreach ($this->stateProperties as $name => $value) {
            $this->stateProperties[$name] = self::normalise($value);
        }
    }

    public function getStateName(): string { return $this->stateName; }
    public function getRawStateProperties(): string { return self::encodeStateProperties($this->stateProperties); }
    public function getMeta(): int { return $this->meta; }
    public function generateStateData(): BlockStateData { return new BlockStateData($this->stateName, $this->stateProperties); }

    /** @return array<string, Tag> */
    public static function decodeStateProperties(string $rawProperties): array
    {
        if ($rawProperties === '') {
            return [];
        }
        $decoded = json_decode($rawProperties, true);
        if (!is_array($decoded)) {
            return [];
        }
        $result = [];
        foreach ($decoded as $name => $value) {
            $result[(string) $name] = self::normalise($value);
        }
        return $result;
    }

    /** @param array<string, Tag|bool|int|string> $properties */
    public static function encodeStateProperties(array $properties): string
    {
        if ($properties === []) {
            return '';
        }
        $values = [];
        foreach ($properties as $name => $value) {
            $values[$name] = $value instanceof Tag ? $value->getValue() : $value;
        }
        ksort($values);
        return json_encode($values, JSON_THROW_ON_ERROR);
    }

    private static function normalise(mixed $value): Tag
    {
        if ($value instanceof Tag) {
            return $value;
        }
        return match (true) {
            is_bool($value) => new ByteTag($value ? 1 : 0),
            is_int($value) => new IntTag($value),
            default => new StringTag((string) $value),
        };
    }
}
