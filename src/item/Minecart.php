<?php

declare(strict_types=1);

namespace pocketmine\item;

class Minecart extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:minecart', 'Minecart'); }
    public function getMaxStackSize(): int { return $this->compatMethod(__FUNCTION__, []); }
}
