<?php

declare(strict_types=1);

namespace pocketmine\event\block;

use pocketmine\block\Block;

class BlockSpreadEvent extends BaseBlockChangeEvent
{
    public function __construct(
        Block $block,
        private Block $source,
        Block $newState,
    ) {
        parent::__construct($block, $newState);
    }

    public function getSource(): Block
    {
        return $this->source;
    }
}
