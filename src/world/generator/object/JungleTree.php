<?php

declare(strict_types=1);

namespace pocketmine\world\generator\object;

use pocketmine\block\Block;

class JungleTree extends Tree
{
    public function __construct()
    {
        parent::__construct(new Block('minecraft:jungle_log', 'Jungle Log'), new Block('minecraft:jungle_leaves', 'Jungle Leaves'), 8);
    }
}
