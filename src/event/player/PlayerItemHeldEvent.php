<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\item\Item;
use pocketmine\player\Player;

class PlayerItemHeldEvent extends PlayerEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(Player $player, private Item $item, private int $hotbarSlot)
    {
        parent::__construct($player);
    }

    public function getSlot(): int { return $this->hotbarSlot; }
    public function getItem(): Item { return clone $this->item; }
}
