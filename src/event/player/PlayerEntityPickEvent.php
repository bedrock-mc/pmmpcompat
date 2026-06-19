<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\entity\Entity;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\item\Item;
use pocketmine\player\Player;

class PlayerEntityPickEvent extends PlayerEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(
        Player $player,
        private Entity $entityClicked,
        private Item $resultItem,
    ) {
        parent::__construct($player);
    }

    public function getEntity(): Entity
    {
        return $this->entityClicked;
    }

    public function getResultItem(): Item
    {
        return clone $this->resultItem;
    }
}
