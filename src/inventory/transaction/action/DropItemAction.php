<?php

declare(strict_types=1);

namespace pocketmine\inventory\transaction\action;

use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\inventory\transaction\TransactionValidationException;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

class DropItemAction extends InventoryAction
{
    public function __construct(Item $targetItem)
    {
        parent::__construct(VanillaItems::AIR(), $targetItem);
    }

    public function validate(Player $source): void
    {
        if ($this->targetItem->isNull()) {
            throw new TransactionValidationException('Cannot drop an empty itemstack');
        }
        if ($this->targetItem->getCount() > $this->targetItem->getMaxStackSize()) {
            throw new TransactionValidationException('Target item exceeds item type max stack size');
        }
    }

    public function onPreExecute(Player $source): bool
    {
        $ev = new PlayerDropItemEvent($source, clone $this->targetItem);
        if ($source->isSpectator()) {
            $ev->cancel();
        }
        $ev->call();
        return !$ev->isCancelled();
    }

    public function execute(Player $source): void
    {
        $source->dropItem(clone $this->targetItem);
    }
}
