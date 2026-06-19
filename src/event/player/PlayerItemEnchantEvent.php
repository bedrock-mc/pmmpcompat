<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\inventory\transaction\EnchantingTransaction;
use pocketmine\item\enchantment\EnchantingOption;
use pocketmine\item\Item;
use pocketmine\player\Player;

class PlayerItemEnchantEvent extends PlayerEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(
        Player $player,
        private EnchantingTransaction $transaction,
        private EnchantingOption $option,
        private Item $inputItem,
        private Item $outputItem,
        private int $cost,
    ) {
        parent::__construct($player);
    }

    public function getTransaction(): EnchantingTransaction
    {
        return $this->transaction;
    }

    public function getOption(): EnchantingOption
    {
        return $this->option;
    }

    public function getInputItem(): Item
    {
        return clone $this->inputItem;
    }

    public function getOutputItem(): Item
    {
        return clone $this->outputItem;
    }

    public function getCost(): int
    {
        return $this->cost;
    }
}
