<?php

declare(strict_types=1);

namespace pocketmine\item;

class Bowl extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:bowl', 'Bowl'); }
    public function getFuelTime(): int { return $this->compatMethod(__FUNCTION__, []); }
}
