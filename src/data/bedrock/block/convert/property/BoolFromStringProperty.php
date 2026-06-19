<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block\convert\property;

use pocketmine\data\bedrock\block\BlockStateSerializeException;
use pocketmine\data\bedrock\block\convert\BlockStateReader;
use pocketmine\data\bedrock\block\convert\BlockStateWriter;

class BoolFromStringProperty implements StringProperty
{
    private string $name;
    private string $falseValue;
    private string $trueValue;
    private \Closure $getter;
    private \Closure $setter;

    public function __construct(string $name, string $falseValue, string $trueValue, \Closure $getter, \Closure $setter)
    {
        $this->name = $name;
        $this->falseValue = $falseValue;
        $this->trueValue = $trueValue;
        $this->getter = $getter;
        $this->setter = $setter;
    }
    public function deserialize(object $block, BlockStateReader $in) : void { $this->deserializePlain($block, $in->readString($this->name)); }
    public function deserializePlain(object $block, string $raw) : void
    {
        ($this->setter)($block, match ($raw) {
            $this->falseValue => false,
            $this->trueValue => true,
            default => throw new BlockStateSerializeException('Invalid value for ' . $this->name . ': ' . $raw),
        });
    }
    public function getName() : string
    {
        return $this->name;
    }

    public function getPossibleValues() : array
    {
        return [$this->falseValue, $this->trueValue];
    }
    public function serialize(object $block, BlockStateWriter $out) : void { $out->writeString($this->name, $this->serializePlain($block)); }
    public function serializePlain(object $block) : string { return ($this->getter)($block) ? $this->trueValue : $this->falseValue; }
}
