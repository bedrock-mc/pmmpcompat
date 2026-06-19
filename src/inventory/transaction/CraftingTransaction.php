<?php

declare(strict_types=1);

namespace pocketmine\inventory\transaction;

use pocketmine\crafting\CraftingManager;
use pocketmine\crafting\CraftingRecipe;
use pocketmine\crafting\RecipeIngredient;
use pocketmine\event\inventory\CraftItemEvent;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\utils\Utils;

class CraftingTransaction extends InventoryTransaction
{
    protected ?CraftingRecipe $recipe;
    protected ?int $repetitions;
    /** @var Item[] */
    protected array $inputs = [];
    /** @var Item[] */
    protected array $outputs = [];

    public function __construct(
        Player $source,
        private CraftingManager $craftingManager,
        array $actions = [],
        ?CraftingRecipe $recipe = null,
        ?int $repetitions = null,
    ) {
        parent::__construct($source, $actions);
        $this->recipe = $recipe;
        $this->repetitions = $repetitions;
    }

    /**
     * @param Item[] $providedItems
     * @param RecipeIngredient[] $recipeIngredients
     */
    public static function matchIngredients(array $providedItems, array $recipeIngredients, int $expectedIterations): void
    {
        if ($recipeIngredients === []) {
            throw new TransactionValidationException('No recipe ingredients given');
        }
        if ($providedItems === []) {
            throw new TransactionValidationException('No transaction items given');
        }

        foreach ($recipeIngredients as $ingredient) {
            $needed = $expectedIterations;
            foreach ($providedItems as $item) {
                if ($needed <= 0) {
                    break;
                }
                if (!$ingredient->accepts($item)) {
                    continue;
                }
                $needed -= $item->getCount();
            }
            if ($needed > 0) {
                throw new TransactionValidationException('Not enough items to satisfy recipe ingredient');
            }
        }
    }

    public function validate(): void
    {
        parent::validate();
        if ($this->recipe === null) {
            foreach ($this->craftingManager->matchRecipeByOutputs($this->outputs) ?? [] as $recipe) {
                if ($recipe instanceof CraftingRecipe) {
                    $this->recipe = $recipe;
                    break;
                }
            }
        }
        if ($this->recipe === null) {
            throw new TransactionValidationException('Unable to match a recipe to transaction');
        }
        $this->repetitions ??= 1;
    }

    protected function callExecuteEvent(): bool
    {
        if ($this->recipe === null) {
            throw new TransactionValidationException('Unable to call craft event without a matched recipe');
        }
        $ev = new CraftItemEvent($this, $this->recipe, $this->repetitions ?? 1, Utils::cloneObjectArray($this->inputs), Utils::cloneObjectArray($this->outputs));
        $ev->call();
        return !$ev->isCancelled();
    }
}
