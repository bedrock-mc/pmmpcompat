<?php

declare(strict_types=1);

namespace pocketmine\crafting;

use pocketmine\item\Item;

class ShapedRecipe implements CraftingRecipe
{
    /** @var list<string> */
    private array $shape;
    /** @var array<string, RecipeIngredient> */
    private array $ingredientList;
    /** @var list<Item> */
    private array $results;
    private int $height;
    private int $width;

    /** @param list<string> $shape @param array<string, RecipeIngredient> $ingredients @param list<Item> $results */
    public function __construct(array $shape, array $ingredients, array $results)
    {
        $shape = array_values($shape);
        $this->height = count($shape);
        if ($this->height < 1 || $this->height > 3) {
            throw new \InvalidArgumentException('Shaped recipes may only have 1, 2 or 3 rows');
        }
        $this->width = strlen($shape[0]);
        if ($this->width < 1 || $this->width > 3) {
            throw new \InvalidArgumentException('Shaped recipes may only have 1, 2 or 3 columns');
        }
        foreach ($shape as $row) {
            if (strlen($row) !== $this->width) {
                throw new \InvalidArgumentException('Shaped recipe rows must all have the same length');
            }
            for ($x = 0; $x < $this->width; $x++) {
                $symbol = $row[$x];
                if ($symbol !== ' ' && !isset($ingredients[$symbol])) {
                    throw new \InvalidArgumentException("No item specified for symbol '$symbol'");
                }
            }
        }
        $this->shape = $shape;
        $this->ingredientList = [];
        foreach ($ingredients as $symbol => $ingredient) {
            if (!$ingredient instanceof RecipeIngredient) {
                throw new \InvalidArgumentException('Recipe ingredients must implement RecipeIngredient');
            }
            $this->ingredientList[(string) $symbol] = $ingredient;
        }
        $this->results = array_map(static fn(Item $item): Item => clone $item, $results);
    }

    public function getHeight(): int { return $this->height; }
    public function getIngredient(int $x, int $y): ?RecipeIngredient { return $this->ingredientList[$this->shape[$y][$x]] ?? null; }
    /** @return RecipeIngredient[] */
    public function getIngredientList(): array
    {
        $ingredients = [];
        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                $ingredient = $this->getIngredient($x, $y);
                if ($ingredient !== null) {
                    $ingredients[] = $ingredient;
                }
            }
        }
        return $ingredients;
    }
    /** @return array<int, array<int, RecipeIngredient|null>> */
    public function getIngredientMap(): array
    {
        $map = [];
        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                $map[$y][$x] = $this->getIngredient($x, $y);
            }
        }
        return $map;
    }
    /** @return Item[] */
    public function getResults(): array { return array_map(static fn(Item $item): Item => clone $item, $this->results); }
    /** @return Item[] */
    public function getResultsFor(CraftingGrid $grid): array { return $this->getResults(); }
    /** @return list<string> */
    public function getShape(): array { return $this->shape; }
    public function getWidth(): int { return $this->width; }
    public function matchesCraftingGrid(CraftingGrid $grid): bool
    {
        if ($this->width !== $grid->getRecipeWidth() || $this->height !== $grid->getRecipeHeight()) {
            return false;
        }
        return $this->matchInputMap($grid, false) || $this->matchInputMap($grid, true);
    }

    private function matchInputMap(CraftingGrid $grid, bool $reverse): bool
    {
        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                $given = $grid->getIngredient($reverse ? $this->width - $x - 1 : $x, $y);
                $required = $this->getIngredient($x, $y);
                if ($required === null) {
                    if (!$given->isNull()) {
                        return false;
                    }
                } elseif (!$required->accepts($given)) {
                    return false;
                }
            }
        }
        return true;
    }
}
