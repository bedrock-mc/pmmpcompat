<?php

declare(strict_types=1);

namespace pocketmine\event\block;

use pocketmine\block\tile\BrewingStand;
use pocketmine\crafting\BrewingRecipe;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\item\Item;

class BrewItemEvent extends BlockEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(
        private BrewingStand $brewingStand,
        private int $slot,
        private Item $input,
        private Item $result,
        private BrewingRecipe $recipe,
    ) {
        parent::__construct($brewingStand->getBlock());
    }

    public function getBrewingStand(): BrewingStand
    {
        return $this->brewingStand;
    }

    public function getSlot(): int
    {
        return $this->slot;
    }

    public function getInput(): Item
    {
        return clone $this->input;
    }

    public function getResult(): Item
    {
        return clone $this->result;
    }

    public function setResult(Item $result): void
    {
        $this->result = clone $result;
    }

    public function getRecipe(): BrewingRecipe
    {
        return $this->recipe;
    }
}
