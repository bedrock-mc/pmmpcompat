<?php

declare(strict_types=1);

namespace pocketmine\item;

class SuspiciousStew extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:suspiciousstew', 'SuspiciousStew'); }
    public function getAdditionalEffects(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getFoodRestore(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getMaxStackSize(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getResidue(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getSaturationRestore(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function requiresHunger(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
