<?php

declare(strict_types=1);

namespace pocketmine\item;

class Boat extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:boat', 'Boat'); }
    public function getFuelTime(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getMaxStackSize(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
