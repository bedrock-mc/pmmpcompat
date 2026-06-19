<?php

declare(strict_types=1);

namespace pocketmine\crafting;

use pocketmine\item\Item;

class ExactRecipeIngredient implements RecipeIngredient
{
    public function __construct(private Item $item)
    {
        if ($item->isNull()) {
            throw new \InvalidArgumentException('Recipe ingredients must not be air items');
        }
        $this->item = clone $item;
        $this->item->setCount(1);
    }

    public function __toString(): string { return 'ExactRecipeIngredient(' . $this->item . ')'; }
    public function accepts(Item $item): bool { return $item->getCount() >= 1 && $this->item->equals($item, true, $this->item->hasNamedTag()); }
    public function getItem(): Item { return clone $this->item; }
}
