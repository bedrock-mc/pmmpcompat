<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

trait StaticSupportTrait
{
    public function canBePlacedAt(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onNearbyBlockChange(): void { $this->compatMethod(__FUNCTION__, []); }
}
