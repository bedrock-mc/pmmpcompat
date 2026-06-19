<?php

declare(strict_types=1);

namespace pocketmine\block;

class ItemFrame extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:itemframe', 'ItemFrame'); }
    public const ROTATIONS = 0;
    public function getDropsForCompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function getFramedItem(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getItemDropChance(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getItemRotation(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getPickedItem(bool $addUserData = false): \pocketmine\item\Item { return $this->compatMethod(__FUNCTION__, [$addUserData]); }
    public function hasMap(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onAttack(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onNearbyBlockChange(): void { $this->compatMethod(__FUNCTION__, []); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function readStateFromWorld(): \pocketmine\block\Block { return $this->compatMethod(__FUNCTION__, []); }
    public function setFramedItem(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setHasMap(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setItemDropChance(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setItemRotation(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function writeStateToWorld(): void { $this->compatMethod(__FUNCTION__, []); }
}
