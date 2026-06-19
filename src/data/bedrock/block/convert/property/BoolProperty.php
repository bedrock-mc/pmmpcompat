<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block\convert\property;

use pocketmine\data\bedrock\block\convert\BlockStateReader;
use pocketmine\data\bedrock\block\convert\BlockStateWriter;

class BoolProperty implements Property
{
    public function __construct(private string $name, private \Closure $getter, private \Closure $setter, private bool $inverted = false) {}
    public function deserialize(object $block, BlockStateReader $in): void { ($this->setter)($block, $in->readBool($this->name) !== $this->inverted); }
    public function getName(): string { return $this->name; }
    public function serialize(object $block, BlockStateWriter $out): void { $out->writeBool($this->name, ((bool) ($this->getter)($block)) !== $this->inverted); }
    public static function unused(string $name, bool $serializedValue): self { return new self($name, static fn(): bool => $serializedValue, static fn(): null => null); }
}
