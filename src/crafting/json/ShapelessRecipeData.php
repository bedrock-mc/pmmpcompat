<?php

declare(strict_types=1);

namespace pocketmine\crafting\json;

class ShapelessRecipeData implements \JsonSerializable
{
    /**
     * @param RecipeIngredientData[] $input
     * @param ItemStackData[] $output
     * @param RecipeIngredientData[] $unlockingIngredients
     */
    public function __construct(
        public array $input,
        public array $output,
        public string $block,
        public int $priority,
        public array $unlockingIngredients = []
    ) {}

    public function jsonSerialize(): array
    {
        $result = (array) $this;
        if ($this->unlockingIngredients === []) {
            unset($result['unlockingIngredients']);
        }
        return $result;
    }
}
