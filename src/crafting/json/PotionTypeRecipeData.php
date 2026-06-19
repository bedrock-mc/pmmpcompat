<?php

declare(strict_types=1);

namespace pocketmine\crafting\json;

class PotionTypeRecipeData
{
    public function __construct(public RecipeIngredientData $input, public RecipeIngredientData $ingredient, public ItemStackData $output) {}
}
