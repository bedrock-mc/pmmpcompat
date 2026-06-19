<?php

declare(strict_types=1);

namespace pocketmine\item;

class EnderPearl extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:enderpearl', 'EnderPearl'); }
    public function getCooldownTag(): ?string { return $this->compatMethod(__FUNCTION__, []); }
    public function getCooldownTicks(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getMaxStackSize(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getThrowForce(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
