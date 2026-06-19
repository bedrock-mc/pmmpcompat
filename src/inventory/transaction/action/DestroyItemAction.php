<?php

declare(strict_types=1);

namespace pocketmine\inventory\transaction\action;

use pocketmine\inventory\transaction\TransactionValidationException;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

class DestroyItemAction extends InventoryAction
{
    public function __construct(Item $targetItem)
    {
        parent::__construct(VanillaItems::AIR(), $targetItem);
    }

    public function validate(Player $source): void
    {
        if ($source->hasFiniteResources()) {
            throw new TransactionValidationException('Player has finite resources, cannot destroy items');
        }
    }

    public function execute(Player $source): void {}
}
