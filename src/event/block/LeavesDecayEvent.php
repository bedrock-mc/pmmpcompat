<?php

declare(strict_types=1);

namespace pocketmine\event\block;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

class LeavesDecayEvent extends BlockEvent implements Cancellable
{
    use CancellableTrait;
}
