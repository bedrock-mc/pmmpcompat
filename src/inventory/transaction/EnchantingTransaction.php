<?php

declare(strict_types=1);

namespace pocketmine\inventory\transaction;

use pocketmine\event\player\PlayerItemEnchantEvent;
use pocketmine\item\enchantment\EnchantingOption;
use pocketmine\item\Item;
use pocketmine\player\Player;

class EnchantingTransaction extends InventoryTransaction
{
    private ?Item $inputItem = null;
    private ?Item $outputItem = null;

    public function __construct(Player $source, private EnchantingOption $option, private int $cost)
    {
        parent::__construct($source);
    }

    public function validate(): void
    {
        parent::validate();
        $actions = $this->getActions();
        if (isset($actions[0])) {
            $this->inputItem = $actions[0]->getSourceItem();
            $this->outputItem = $actions[0]->getTargetItem();
        }
        if ($this->inputItem === null || $this->inputItem->isNull()) {
            throw new TransactionValidationException('No item to enchant received');
        }
        if ($this->outputItem === null || $this->outputItem->isNull()) {
            throw new TransactionValidationException('No enchanted output item received');
        }
    }

    public function execute(): void
    {
        parent::execute();
    }

    protected function callExecuteEvent(): bool
    {
        if ($this->inputItem === null || $this->outputItem === null) {
            throw new TransactionValidationException('Unable to call enchant event before validation');
        }
        $event = new PlayerItemEnchantEvent($this->source, $this, $this->option, $this->inputItem, $this->outputItem, $this->cost);
        $event->call();
        return !$event->isCancelled();
    }
}
