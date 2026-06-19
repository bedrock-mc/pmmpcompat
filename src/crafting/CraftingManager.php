<?php

declare(strict_types=1);

namespace pocketmine\crafting;

use pocketmine\item\Item;
use pocketmine\utils\ObjectSet;

class CraftingManager
{
    /** @var array<string, list<ShapedRecipe>> */
    private array $shapedRecipes = [];
    /** @var array<string, list<ShapelessRecipe>> */
    private array $shapelessRecipes = [];
    /** @var list<CraftingRecipe> */
    private array $craftingRecipeIndex = [];
    /** @var array<string, FurnaceRecipeManager> */
    private array $furnaceRecipeManagers = [];
    /** @var list<PotionContainerChangeRecipe> */
    private array $potionContainerChangeRecipes = [];
    /** @var list<PotionTypeRecipe> */
    private array $potionTypeRecipes = [];
    private ObjectSet $recipeRegisteredCallbacks;

    public function __construct()
    {
        $this->recipeRegisteredCallbacks = new ObjectSet();
        foreach (FurnaceType::cases() as $type) {
            $manager = new FurnaceRecipeManager();
            $callbacks = $this->recipeRegisteredCallbacks;
            $manager->getRecipeRegisteredCallbacks()->add(static function(FurnaceRecipe $recipe) use ($callbacks): void {
                foreach ($callbacks as $callback) {
                    if ($callback instanceof \Closure) {
                        $callback();
                    }
                }
            });
            $this->furnaceRecipeManagers[$type->name] = $manager;
        }
    }

    public function getCraftingRecipeFromIndex(int $index): ?CraftingRecipe { return $this->craftingRecipeIndex[$index] ?? null; }
    /** @return list<CraftingRecipe> */
    public function getCraftingRecipeIndex(): array { return $this->craftingRecipeIndex; }
    public function getFurnaceRecipeManager(FurnaceType $furnaceType): FurnaceRecipeManager { return $this->furnaceRecipeManagers[$furnaceType->name]; }
    /** @return list<PotionContainerChangeRecipe> */
    public function getPotionContainerChangeRecipes(): array { return $this->potionContainerChangeRecipes; }
    /** @return list<PotionTypeRecipe> */
    public function getPotionTypeRecipes(): array { return $this->potionTypeRecipes; }
    public function getRecipeRegisteredCallbacks(): ObjectSet { return $this->recipeRegisteredCallbacks; }
    /** @return array<string, list<ShapedRecipe>> */
    public function getShapedRecipes(): array { return $this->shapedRecipes; }
    /** @return array<string, list<ShapelessRecipe>> */
    public function getShapelessRecipes(): array { return $this->shapelessRecipes; }
    public function matchBrewingRecipe(Item $input, Item $ingredient): ?BrewingRecipe
    {
        foreach ($this->potionContainerChangeRecipes as $recipe) {
            if ($recipe->getIngredient()->accepts($ingredient) && $recipe->getResultFor($input) !== null) {
                return $recipe;
            }
        }
        foreach ($this->potionTypeRecipes as $recipe) {
            if ($recipe->getIngredient()->accepts($ingredient) && $recipe->getResultFor($input) !== null) {
                return $recipe;
            }
        }
        return null;
    }
    /** @param Item[] $outputs */
    public function matchRecipe(CraftingGrid $grid, array $outputs): ?CraftingRecipe
    {
        foreach ($this->matchRecipeByOutputs($outputs) as $recipe) {
            if ($recipe->matchesCraftingGrid($grid)) {
                return $recipe;
            }
        }
        return null;
    }
    /** @param Item[] $outputs @return \Generator<int, CraftingRecipe> */
    public function matchRecipeByOutputs(array $outputs): \Generator
    {
        $hash = self::hashOutputs($outputs);
        foreach ($this->shapedRecipes[$hash] ?? [] as $recipe) {
            yield $recipe;
        }
        foreach ($this->shapelessRecipes[$hash] ?? [] as $recipe) {
            yield $recipe;
        }
    }
    public function registerPotionContainerChangeRecipe(PotionContainerChangeRecipe $recipe): void
    {
        $this->potionContainerChangeRecipes[] = $recipe;
        $this->notifyRecipeRegistered();
    }
    public function registerPotionTypeRecipe(PotionTypeRecipe $recipe): void
    {
        $this->potionTypeRecipes[] = $recipe;
        $this->notifyRecipeRegistered();
    }
    public function registerShapedRecipe(ShapedRecipe $recipe): void
    {
        $this->shapedRecipes[self::hashOutputs($recipe->getResults())][] = $recipe;
        $this->craftingRecipeIndex[] = $recipe;
        $this->notifyRecipeRegistered();
    }
    public function registerShapelessRecipe(ShapelessRecipe $recipe): void
    {
        $this->shapelessRecipes[self::hashOutputs($recipe->getResults())][] = $recipe;
        $this->craftingRecipeIndex[] = $recipe;
        $this->notifyRecipeRegistered();
    }
    public static function sort(Item $a, Item $b): int { return ($a->getStateId() <=> $b->getStateId()) ?: ($a->getCount() <=> $b->getCount()); }

    /** @param Item[] $outputs */
    private static function hashOutputs(array $outputs): string
    {
        $parts = array_map(static fn(Item $item): string => $item->getTypeId() . ':' . $item->getCount(), $outputs);
        sort($parts);
        return implode('|', $parts);
    }

    private function notifyRecipeRegistered(): void
    {
        foreach ($this->recipeRegisteredCallbacks as $callback) {
            if ($callback instanceof \Closure) {
                $callback();
            }
        }
    }
}
