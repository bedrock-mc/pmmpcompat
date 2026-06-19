<?php

declare(strict_types=1);

namespace pocketmine\item;

class Bow extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:bow', 'Bow'); }
    public function canStartUsingItem(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getFuelTime(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getMaxDurability(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onReleaseUsing(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
}
