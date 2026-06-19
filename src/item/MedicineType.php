<?php

declare(strict_types=1);

namespace pocketmine\item;

enum MedicineType
{
    case STUB;
    public function getCuredEffect(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getDisplayName(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
