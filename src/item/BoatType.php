<?php

declare(strict_types=1);

namespace pocketmine\item;

enum BoatType
{
    case STUB;
    public function getDisplayName(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getWoodType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
