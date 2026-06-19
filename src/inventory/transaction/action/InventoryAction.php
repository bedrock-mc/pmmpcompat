<?php

declare(strict_types=1);

namespace pocketmine\inventory\transaction\action;

use pocketmine\inventory\transaction\InventoryTransaction;
use pocketmine\item\Item;
use pocketmine\player\Player;

class InventoryAction
{
    public function __construct(protected Item $sourceItem, protected Item $targetItem) {}

    public function getSourceItem(): Item
    {
        return clone $this->sourceItem;
    }

    public function getTargetItem(): Item
    {
        return clone $this->targetItem;
    }

    public function onAddToTransaction(InventoryTransaction $transaction): void {}

    public function validate(Player $source): void {}

    public function onPreExecute(Player $source): bool
    {
        return true;
    }

    public function execute(Player $source): void {}
}
