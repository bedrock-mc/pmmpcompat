<?php

declare(strict_types=1);

namespace pocketmine\event\inventory;

use pocketmine\crafting\CraftingRecipe;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;
use pocketmine\inventory\transaction\CraftingTransaction;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\utils\Utils;

class CraftItemEvent extends Event implements Cancellable
{
    use CancellableTrait;

    /**
     * @param Item[] $inputs
     * @param Item[] $outputs
     */
    public function __construct(
        private CraftingTransaction $transaction,
        private CraftingRecipe $recipe,
        private int $repetitions,
        private array $inputs,
        private array $outputs,
    ) {}

    public function getTransaction(): CraftingTransaction
    {
        return $this->transaction;
    }

    public function getRecipe(): CraftingRecipe
    {
        return $this->recipe;
    }

    public function getRepetitions(): int
    {
        return $this->repetitions;
    }

    /** @return Item[] */
    public function getInputs(): array
    {
        return Utils::cloneObjectArray($this->inputs);
    }

    /** @return Item[] */
    public function getOutputs(): array
    {
        return Utils::cloneObjectArray($this->outputs);
    }

    public function getPlayer(): Player
    {
        return $this->transaction->getSource();
    }
}
