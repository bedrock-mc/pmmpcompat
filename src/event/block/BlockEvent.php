<?php

declare(strict_types=1);

namespace pocketmine\event\block;

use pocketmine\block\Block;
use pocketmine\event\Event;

abstract class BlockEvent extends Event
{
    public function __construct(protected Block $block) {}

    public function getBlock(): Block
    {
        return $this->block;
    }
}
