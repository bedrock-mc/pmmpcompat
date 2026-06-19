<?php

declare(strict_types=1);

namespace pocketmine\block;

class BaseBanner extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:basebanner', 'BaseBanner'); }
    public function asItem(): \pocketmine\item\Item { return $this->compatMethod(__FUNCTION__, []); }
    public function getDropsForCompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function getMaxStackSize(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getPatterns(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getPickedItem(bool $addUserData = false): \pocketmine\item\Item { return $this->compatMethod(__FUNCTION__, [$addUserData]); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isSolid(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function onNearbyBlockChange(): void { $this->compatMethod(__FUNCTION__, []); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function readStateFromWorld(): \pocketmine\block\Block { return $this->compatMethod(__FUNCTION__, []); }
    public function setPatterns(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function writeStateToWorld(): void { $this->compatMethod(__FUNCTION__, []); }
}
