<?php

declare(strict_types=1);

namespace pocketmine\block;

class FlowerPot extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:flowerpot', 'FlowerPot'); }
    public function canAddPlant(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getDropsForCompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function getPickedItem(bool $addUserData = false): \pocketmine\item\Item { return $this->compatMethod(__FUNCTION__, [$addUserData]); }
    public function getPlant(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function readStateFromWorld(): \pocketmine\block\Block { return $this->compatMethod(__FUNCTION__, []); }
    public function setPlant(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function writeStateToWorld(): void { $this->compatMethod(__FUNCTION__, []); }
}
