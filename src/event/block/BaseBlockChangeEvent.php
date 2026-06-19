<?php

declare(strict_types=1);

namespace pocketmine\event\block;

use pocketmine\block\Block;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

abstract class BaseBlockChangeEvent extends BlockEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(
        Block $block,
        private Block $newState,
    ) {
        parent::__construct($block);
    }

    public function getNewState(): Block
    {
        return $this->newState;
    }
}
