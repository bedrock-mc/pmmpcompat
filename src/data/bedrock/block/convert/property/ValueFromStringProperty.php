<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block\convert\property;

use pocketmine\data\bedrock\block\convert\BlockStateReader;
use pocketmine\data\bedrock\block\convert\BlockStateWriter;

class ValueFromStringProperty implements StringProperty
{
    public function __construct(private string $name, private StateMap $map, private \Closure $getter, private \Closure $setter) {}
    public function deserialize(object $block, BlockStateReader $in): void { $this->deserializePlain($block, $in->readString($this->name)); }
    public function deserializePlain(object $block, string $raw): void
    {
        $value = $this->map->rawToValue($raw);
        if ($value === null) {
            throw new \InvalidArgumentException("Property \"$this->name\" has invalid value \"$raw\"");
        }
        ($this->setter)($block, $value);
    }
    public function getName(): string { return $this->name; }
    public function getPossibleValues(): array { return array_map('strval', array_keys($this->map->getRawToValueMap())); }
    public function serialize(object $block, BlockStateWriter $out): void { $out->writeString($this->name, $this->serializePlain($block)); }
    public function serializePlain(object $block): string { return (string) $this->map->valueToRaw(($this->getter)($block)); }
}
