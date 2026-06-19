<?php

declare(strict_types=1);

namespace pocketmine\item;

interface Releasable
{
    public function canStartUsingItem(mixed ...$args): mixed;
}
