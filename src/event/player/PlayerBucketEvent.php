<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\block\Block;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\item\Item;
use pocketmine\player\Player;

abstract class PlayerBucketEvent extends PlayerEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(
        Player $who,
        private Block $blockClicked,
        private int $blockFace,
        private Item $bucket,
        private Item $itemInHand,
    ) {
        parent::__construct($who);
    }

    public function getBucket(): Item
    {
        return $this->bucket;
    }

    public function getItem(): Item
    {
        return $this->itemInHand;
    }

    public function setItem(Item $item): void
    {
        $this->itemInHand = $item;
    }

    public function getBlockClicked(): Block
    {
        return $this->blockClicked;
    }

    public function getBlockFace(): int
    {
        return $this->blockFace;
    }
}
