<?php

declare(strict_types=1);

namespace pocketmine\crafting\json;

class SmithingTransformRecipeData
{
    public function __construct(
        public RecipeIngredientData $template,
        public RecipeIngredientData $input,
        public RecipeIngredientData $addition,
        public ItemStackData $output,
        public string $block
    ) {}
}
