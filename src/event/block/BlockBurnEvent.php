<?php

declare(strict_types=1);

namespace pocketmine\event\block;

use pocketmine\block\Block;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

class BlockBurnEvent extends BlockEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(
        Block $block,
        private Block $causingBlock,
    ) {
        parent::__construct($block);
    }

    public function getCausingBlock(): Block
    {
        return $this->causingBlock;
    }
}
