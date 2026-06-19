<?php

declare(strict_types=1);

namespace pocketmine\block;

class WaterCauldron extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:watercauldron', 'WaterCauldron'); }
    public const CLEAN_ARMOR_USE_AMOUNT = 0;
    public const CLEAN_BANNER_USE_AMOUNT = 0;
    public const CLEAN_SHULKER_BOX_USE_AMOUNT = 0;
    public const DYE_ARMOR_USE_AMOUNT = 0;
    public const ENTITY_EXTINGUISH_USE_AMOUNT = 0;
    public const WATER_BOTTLE_FILL_AMOUNT = 0;
    public function getCustomWaterColor(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getEmptySound(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getFillSound(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function hasEntityCollision(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function onEntityInside(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onNearbyBlockChange(): void { $this->compatMethod(__FUNCTION__, []); }
    public function readStateFromWorld(): \pocketmine\block\Block { return $this->compatMethod(__FUNCTION__, []); }
    public function setCustomWaterColor(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function writeStateToWorld(): void { $this->compatMethod(__FUNCTION__, []); }
}
