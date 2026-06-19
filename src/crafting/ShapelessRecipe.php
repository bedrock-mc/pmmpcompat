<?php

declare(strict_types=1);

namespace pocketmine\crafting;

use pocketmine\item\Item;

class ShapelessRecipe implements CraftingRecipe
{
    /** @var list<RecipeIngredient> */
    private array $ingredients;
    /** @var list<Item> */
    private array $results;

    /** @param list<RecipeIngredient> $ingredients @param list<Item> $results */
    public function __construct(array $ingredients, array $results, private ShapelessRecipeType $type = ShapelessRecipeType::CRAFTING)
    {
        if (count($ingredients) > 9) {
            throw new \InvalidArgumentException('Shapeless recipes cannot have more than 9 ingredients');
        }
        foreach ($ingredients as $ingredient) {
            if (!$ingredient instanceof RecipeIngredient) {
                throw new \InvalidArgumentException('Recipe ingredients must implement RecipeIngredient');
            }
        }
        $this->ingredients = array_values($ingredients);
        $this->results = array_map(static fn(Item $item): Item => clone $item, $results);
    }

    public function getIngredientCount(): int { return count($this->ingredients); }
    /** @return RecipeIngredient[] */
    public function getIngredientList(): array { return $this->ingredients; }
    /** @return Item[] */
    public function getResults(): array { return array_map(static fn(Item $item): Item => clone $item, $this->results); }
    /** @return Item[] */
    public function getResultsFor(CraftingGrid $grid): array { return $this->getResults(); }
    public function getType(): ShapelessRecipeType { return $this->type; }
    public function matchesCraftingGrid(CraftingGrid $grid): bool
    {
        $input = $grid->getContents();
        foreach ($this->ingredients as $ingredient) {
            foreach ($input as $slot => $haveItem) {
                if ($ingredient->accepts($haveItem)) {
                    unset($input[$slot]);
                    continue 2;
                }
            }
            return false;
        }
        return count($input) === 0;
    }
}
