<?php

declare(strict_types=1);

namespace pocketmine\crafting\json;

class PotionContainerChangeRecipeData
{
    public function __construct(public string $input_item_name, public RecipeIngredientData $ingredient, public string $output_item_name) {}
}
