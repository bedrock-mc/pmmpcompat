<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block\convert\property;

use pocketmine\data\bedrock\block\convert\BlockStateReader;
use pocketmine\data\bedrock\block\convert\BlockStateWriter;

class ValueSetFromIntProperty implements Property
{
    private int $maxValue = 0;

    public function __construct(private string $name, private StateMap $map, private \Closure $getter, private \Closure $setter)
    {
        foreach (array_keys($this->map->getRawToValueMap()) as $flag) {
            $this->maxValue |= (int) $flag;
        }
    }

    public function deserialize(object $block, BlockStateReader $in): void
    {
        $flags = $in->readBoundedInt($this->name, 0, $this->maxValue);
        $values = [];
        foreach ($this->map->getRawToValueMap() as $flag => $value) {
            if (((int) $flag & $flags) === (int) $flag) {
                $values[] = $value;
            }
        }
        ($this->setter)($block, $values);
    }
    public function getName(): string { return $this->name; }
    public function serialize(object $block, BlockStateWriter $out): void
    {
        $flags = 0;
        foreach (($this->getter)($block) as $value) {
            $flags |= (int) $this->map->valueToRaw($value);
        }
        $out->writeInt($this->name, $flags);
    }
}
