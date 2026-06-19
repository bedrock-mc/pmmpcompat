<?php

declare(strict_types=1);

namespace pocketmine\crafting;

use pocketmine\item\Item;

class PotionTypeRecipe implements BrewingRecipe
{
    public function __construct(private RecipeIngredient $input, private RecipeIngredient $ingredient, private Item $output)
    {
        $this->output = clone $output;
    }

    public function getIngredient(): RecipeIngredient { return $this->ingredient; }
    public function getInput(): RecipeIngredient { return $this->input; }
    public function getOutput(): Item { return clone $this->output; }
    public function getResultFor(Item $input): ?Item { return $this->input->accepts($input) ? $this->getOutput() : null; }
}
