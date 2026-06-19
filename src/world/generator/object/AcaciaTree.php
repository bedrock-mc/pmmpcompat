<?php

declare(strict_types=1);

namespace pocketmine\world\generator\object;

use pocketmine\block\Block;

final class AcaciaTree extends Tree
{
    public function __construct()
    {
        parent::__construct(new Block('minecraft:acacia_log', 'Acacia Log'), new Block('minecraft:acacia_leaves', 'Acacia Leaves'), 6);
    }
}
