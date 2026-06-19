<?php

declare(strict_types=1);

namespace pocketmine\block;

class BaseOminousBanner extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:baseominousbanner', 'BaseOminousBanner'); }
    public function asItem(): \pocketmine\item\Item { return $this->compatMethod(__FUNCTION__, []); }
    public function getFuelTime(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getMaxStackSize(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isSolid(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function onNearbyBlockChange(): void { $this->compatMethod(__FUNCTION__, []); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function writeStateToWorld(): void { $this->compatMethod(__FUNCTION__, []); }
}
