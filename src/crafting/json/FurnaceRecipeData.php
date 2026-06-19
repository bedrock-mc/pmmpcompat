<?php

declare(strict_types=1);

namespace pocketmine\crafting\json;

class FurnaceRecipeData
{
    public function __construct(public RecipeIngredientData $input, public ItemStackData $output, public string $block) {}
}
