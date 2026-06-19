<?php

declare(strict_types=1);

namespace pocketmine\crafting;

use pocketmine\data\bedrock\ItemTagToIdMap;
use pocketmine\item\Item;

class TagWildcardRecipeIngredient implements RecipeIngredient
{
    public function __construct(private string $tagName) {}
    public function __toString(): string { return 'TagWildcardRecipeIngredient(' . $this->tagName . ')'; }
    public function accepts(Item $item): bool { return $item->getCount() >= 1 && ItemTagToIdMap::getInstance()->tagContainsId($this->tagName, $item->getTypeId()); }
    public function getTagName(): string { return $this->tagName; }
}
