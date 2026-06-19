<?php

declare(strict_types=1);

namespace pocketmine\event\block;

use pocketmine\block\Block;

class BlockFormEvent extends BaseBlockChangeEvent
{
    public function __construct(
        Block $block,
        Block $newState,
        private Block $causingBlock,
    ) {
        parent::__construct($block, $newState);
    }

    public function getCausingBlock(): Block
    {
        return $this->causingBlock;
    }
}
