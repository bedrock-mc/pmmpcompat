<?php

declare(strict_types=1);

namespace pocketmine\event\block;

use pocketmine\block\Block;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;
use pocketmine\world\BlockTransaction;

class StructureGrowEvent extends BlockEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(
        Block $block,
        private BlockTransaction $transaction,
        private ?Player $player,
    ) {
        parent::__construct($block);
    }

    public function getTransaction(): BlockTransaction
    {
        return $this->transaction;
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }
}
