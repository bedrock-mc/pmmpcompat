<?php

declare(strict_types=1);

namespace pocketmine\item;

class Coal extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:coal', 'Coal'); }
    public function getFuelTime(): int { return $this->compatMethod(__FUNCTION__, []); }
}
