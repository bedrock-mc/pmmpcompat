<?php

declare(strict_types=1);

namespace pocketmine\item;

class LiquidBucket extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:liquidbucket', 'LiquidBucket'); }
    public function getFuelResidue(): ?Item { return $this->compatMethod(__FUNCTION__, []); }
    public function getFuelTime(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getLiquid(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getMaxStackSize(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function onInteractBlock(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
}
