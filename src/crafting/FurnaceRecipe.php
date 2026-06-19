<?php

declare(strict_types=1);

namespace pocketmine\crafting;

use pocketmine\item\Item;

class FurnaceRecipe
{
    public function __construct(private Item $result, private RecipeIngredient $ingredient)
    {
        $this->result = clone $result;
    }

    public function getInput(): RecipeIngredient { return $this->ingredient; }
    public function getResult(): Item { return clone $this->result; }
}
