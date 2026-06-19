<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class PlayerItemUseEvent extends PlayerEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(Player $player, private Item $item, private ?Vector3 $directionVector = null)
    {
        parent::__construct($player);
    }

    public function getItem(): Item
    {
        return $this->item;
    }

    public function getDirectionVector(): Vector3
    {
        return $this->directionVector ?? Vector3::zero();
    }
}
