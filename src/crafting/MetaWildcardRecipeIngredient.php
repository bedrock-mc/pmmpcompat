<?php

declare(strict_types=1);

namespace pocketmine\crafting;

use pocketmine\item\Item;

class MetaWildcardRecipeIngredient implements RecipeIngredient
{
    public function __construct(private string $itemId) {}
    public function __toString(): string { return 'MetaWildcardRecipeIngredient(' . $this->itemId . ')'; }
    public function accepts(Item $item): bool { return $item->getCount() >= 1 && $item->getTypeId() === $this->itemId; }
    public function getItemId(): string { return $this->itemId; }
}
