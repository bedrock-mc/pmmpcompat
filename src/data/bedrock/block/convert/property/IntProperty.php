<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block\convert\property;

use pocketmine\data\bedrock\block\convert\BlockStateReader;
use pocketmine\data\bedrock\block\convert\BlockStateWriter;

class IntProperty implements Property
{
    public function __construct(private string $name, private int $min, private int $max, private \Closure $getter, private \Closure $setter) {}
    public function deserialize(object $block, BlockStateReader $in): void { ($this->setter)($block, $in->readBoundedInt($this->name, $this->min, $this->max)); }
    public function getName(): string { return $this->name; }
    public function serialize(object $block, BlockStateWriter $out): void { $out->writeInt($this->name, (int) ($this->getter)($block)); }
    public static function unused(string $name, int $serializedValue): self { return new self($name, $serializedValue, $serializedValue, static fn(): int => $serializedValue, static fn(): null => null); }
}
