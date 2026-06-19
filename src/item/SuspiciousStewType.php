<?php

declare(strict_types=1);

namespace pocketmine\item;

enum SuspiciousStewType
{
    case STUB;
    public function getEffects(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
