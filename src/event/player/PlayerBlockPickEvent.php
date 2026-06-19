<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\block\Block;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\item\Item;
use pocketmine\player\Player;

class PlayerBlockPickEvent extends PlayerEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(
        Player $player,
        private Block $blockClicked,
        private Item $resultItem,
    ) {
        parent::__construct($player);
    }

    public function getBlock(): Block
    {
        return $this->blockClicked;
    }

    public function getResultItem(): Item
    {
        return clone $this->resultItem;
    }
}
