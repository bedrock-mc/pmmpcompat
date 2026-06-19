<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

trait FacesOppositePlacingPlayerTrait
{
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
}
