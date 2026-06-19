<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

trait MultiAnySupportTrait
{
    public function onNearbyBlockChange(): void { $this->compatMethod(__FUNCTION__, []); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
}
