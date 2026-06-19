<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block\convert;

use pocketmine\block\Block;
use pocketmine\data\bedrock\block\convert\property\Property;

class Model
{
    /** @var list<Property> */
    private array $properties = [];

    private function __construct(private Block $block, private string $id) {}

    public static function create(Block $block, string $id): self { return new self($block, $id); }
    public function getBlock(): Block { return $this->block; }
    public function getId(): string { return $this->id; }
    /** @return list<Property> */
    public function getProperties(): array { return $this->properties; }
    /** @param list<Property> $properties */
    public function properties(array $properties): self { $this->properties = $properties; return $this; }
}
