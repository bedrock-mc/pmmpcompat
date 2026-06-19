<?php

declare(strict_types=1);

namespace pocketmine\crafting\json;

class SmithingTrimRecipeData
{
    public function __construct(
        public RecipeIngredientData $template,
        public RecipeIngredientData $input,
        public RecipeIngredientData $addition,
        public string $block
    ) {}
}
