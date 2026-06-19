<?php

declare(strict_types=1);

namespace pocketmine\event\block;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;
use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class BlockPlaceEvent extends Event implements Cancellable
{
    use CancellableTrait;

    public function __construct(private Player $player, private Vector3|Block $blockPosition, private mixed $transaction = null, private ?Block $blockAgainst = null, private ?Item $item = null) {}

    public function getPlayer(): Player { return $this->player; }
    public function getBlockPosition(): Vector3 { return $this->blockPosition instanceof Block ? ($this->blockPosition->getPosition() ?? Vector3::zero()) : $this->blockPosition; }
    public function getBlockAgainst(): Block { return $this->blockAgainst ?? VanillaBlocks::AIR(); }
    public function getItem(): Item { return clone ($this->item ?? VanillaItems::AIR()); }
    public function getTransaction(): mixed { return $this->transaction; }
}
