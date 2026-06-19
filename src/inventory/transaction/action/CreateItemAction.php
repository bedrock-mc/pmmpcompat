<?php

declare(strict_types=1);

namespace pocketmine\inventory\transaction\action;

use pocketmine\inventory\transaction\TransactionValidationException;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

class CreateItemAction extends InventoryAction
{
    public function __construct(Item $sourceItem)
    {
        parent::__construct($sourceItem, VanillaItems::AIR());
    }

    public function validate(Player $source): void
    {
        if ($source->hasFiniteResources()) {
            throw new TransactionValidationException('Player has finite resources, cannot create items');
        }
        if (!$source->getCreativeInventory()->contains($this->sourceItem)) {
            throw new TransactionValidationException('Creative inventory does not contain requested item');
        }
    }

    public function execute(Player $source): void {}
}
