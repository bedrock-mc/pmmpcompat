<?php

declare(strict_types=1);

namespace pocketmine\item;

class Spyglass extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:spyglass', 'Spyglass'); }
    public function canStartUsingItem(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getMaxStackSize(): int { return $this->compatMethod(__FUNCTION__, []); }
}
