<?php

declare(strict_types=1);

namespace pocketmine\crafting;

use pocketmine\item\Item;

class PotionContainerChangeRecipe implements BrewingRecipe
{
    public function __construct(private string $inputItemId, private RecipeIngredient $ingredient, private string $outputItemId) {}
    public function getIngredient(): RecipeIngredient { return $this->ingredient; }
    public function getInputItemId(): string { return $this->inputItemId; }
    public function getOutputItemId(): string { return $this->outputItemId; }
    public function getResultFor(Item $input): ?Item
    {
        return $input->getTypeId() === $this->inputItemId ? new Item($this->outputItemId, $this->outputItemId, $input->getCount()) : null;
    }
}
