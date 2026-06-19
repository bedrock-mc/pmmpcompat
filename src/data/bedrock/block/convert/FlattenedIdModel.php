<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block\convert;

use pocketmine\block\Block;
use pocketmine\data\bedrock\block\convert\property\Property;
use pocketmine\data\bedrock\block\convert\property\StringProperty;

class FlattenedIdModel
{
    /** @var list<string|StringProperty> */
    private array $idComponents = [];
    /** @var list<Property> */
    private array $properties = [];

    private function __construct(private Block $block) {}

    public static function create(Block $block): self { return new self($block); }
    public function getBlock(): Block { return $this->block; }
    /** @return list<string|StringProperty> */
    public function getIdComponents(): array { return $this->idComponents; }
    /** @return list<Property> */
    public function getProperties(): array { return $this->properties; }
    /** @param non-empty-list<string|StringProperty> $components */
    public function idComponents(array $components): self { $this->idComponents = $components; return $this; }
    /** @param non-empty-list<Property> $properties */
    public function properties(array $properties): self { $this->properties = $properties; return $this; }
}
