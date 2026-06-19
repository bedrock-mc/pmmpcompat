<?php

declare(strict_types=1);

namespace pocketmine\crafting;

use pocketmine\item\Item;

interface CraftingRecipe
{
    /** @return RecipeIngredient[] */
    public function getIngredientList(): array;
    /** @return Item[] */
    public function getResultsFor(CraftingGrid $grid): array;
    public function matchesCraftingGrid(CraftingGrid $grid): bool;
}
