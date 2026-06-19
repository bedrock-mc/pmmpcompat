<?php

declare(strict_types=1);

namespace pocketmine\crafting;

use pocketmine\item\Item;
use pocketmine\utils\ObjectSet;

class FurnaceRecipeManager
{
    /** @var list<FurnaceRecipe> */
    private array $recipes = [];
    /** @var array<int, FurnaceRecipe> */
    private array $lookupCache = [];
    private ObjectSet $recipeRegisteredCallbacks;

    public function __construct()
    {
        $this->recipeRegisteredCallbacks = new ObjectSet();
    }

    public function Item(Item $input): ?FurnaceRecipe { return $this->match($input); }
    /** @return list<FurnaceRecipe> */
    public function getAll(): array { return $this->recipes; }
    public function getRecipeRegisteredCallbacks(): ObjectSet { return $this->recipeRegisteredCallbacks; }
    public function register(FurnaceRecipe $recipe): void
    {
        $this->recipes[] = $recipe;
        $this->lookupCache = [];
        foreach ($this->recipeRegisteredCallbacks as $callback) {
            if ($callback instanceof \Closure) {
                $callback($recipe);
            }
        }
    }

    public function match(Item $input): ?FurnaceRecipe
    {
        $index = $input->getStateId();
        if (isset($this->lookupCache[$index])) {
            return $this->lookupCache[$index];
        }
        foreach ($this->recipes as $recipe) {
            if ($recipe->getInput()->accepts($input)) {
                return $this->lookupCache[$index] = $recipe;
            }
        }
        return null;
    }
}
