<?php

declare(strict_types=1);

namespace pocketmine\item;

interface ConsumableItem
{
    public function getResidue(mixed ...$args): mixed;
}
