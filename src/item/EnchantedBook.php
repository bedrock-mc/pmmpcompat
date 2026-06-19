<?php

declare(strict_types=1);

namespace pocketmine\item;

class EnchantedBook extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:enchantedbook', 'EnchantedBook'); }
    public function getMaxStackSize(): int { return $this->compatMethod(__FUNCTION__, []); }
}
