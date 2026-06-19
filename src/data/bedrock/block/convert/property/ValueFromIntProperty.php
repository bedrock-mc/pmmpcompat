<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block\convert\property;

use pocketmine\data\bedrock\block\convert\BlockStateReader;
use pocketmine\data\bedrock\block\convert\BlockStateWriter;

class ValueFromIntProperty implements Property
{
    public function __construct(private string $name, private StateMap $map, private \Closure $getter, private \Closure $setter) {}
    public function deserialize(object $block, BlockStateReader $in): void
    {
        $raw = $in->readInt($this->name);
        $value = $this->map->rawToValue($raw);
        if ($value === null) {
            throw $in->badValueException($this->name, (string) $raw);
        }
        ($this->setter)($block, $value);
    }
    public function getName(): string { return $this->name; }
    public function getPossibleValues(): array { return array_keys($this->map->getRawToValueMap()); }
    public function serialize(object $block, BlockStateWriter $out): void { $out->writeInt($this->name, (int) $this->map->valueToRaw(($this->getter)($block))); }
}
