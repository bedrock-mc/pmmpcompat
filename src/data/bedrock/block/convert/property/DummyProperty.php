<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block\convert\property;

use pocketmine\data\bedrock\block\convert\BlockStateReader;
use pocketmine\data\bedrock\block\convert\BlockStateWriter;

class DummyProperty implements Property
{
    public function __construct(private string $name, private bool|int|string $value) {}
    public function deserialize(object $block, BlockStateReader $in): void { $in->ignored($this->name); }
    public function getName(): string { return $this->name; }
    public function serialize(object $block, BlockStateWriter $out): void
    {
        if (is_bool($this->value)) {
            $out->writeBool($this->name, $this->value);
        } elseif (is_int($this->value)) {
            $out->writeInt($this->name, $this->value);
        } else {
            $out->writeString($this->name, $this->value);
        }
    }
}
