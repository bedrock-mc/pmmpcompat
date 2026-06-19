<?php

declare(strict_types=1);

namespace pocketmine\event\block;

use pocketmine\block\Block;
use pocketmine\player\Player;

class BlockGrowEvent extends BaseBlockChangeEvent
{
    public function __construct(
        Block $block,
        Block $newState,
        private ?Player $player = null,
    ) {
        parent::__construct($block, $newState);
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }
}
