<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;

class Farmland extends Block
{
    public const MAX_WETNESS = 7;

    private const WATER_SEARCH_HORIZONTAL_LENGTH = 9;
    private const WATER_SEARCH_VERTICAL_LENGTH = 2;
    private const WATER_POSITION_INDICES_TOTAL = (self::WATER_SEARCH_HORIZONTAL_LENGTH ** 2) * self::WATER_SEARCH_VERTICAL_LENGTH;

    private int $wetness = 0;
    private int $waterPositionIndex = -1;

    public function __construct()
    {
        parent::__construct('minecraft:farmland', 'Farmland');
    }

    /** @return Item[] */
    public function getDropsForCompatibleTool(Item $item): array
    {
        return [VanillaItems::DIRT()];
    }

    public function getPickedItem(bool $addUserData = false): Item
    {
        return VanillaItems::DIRT();
    }

    public function getWetness(): int
    {
        return $this->wetness;
    }

    public function setWetness(int $wetness): self
    {
        if ($wetness < 0 || $wetness > self::MAX_WETNESS) {
            throw new \InvalidArgumentException('Wetness must be in range 0 ... ' . self::MAX_WETNESS);
        }
        $this->wetness = $wetness;
        return $this;
    }

    public function getWaterPositionIndex(): int
    {
        return $this->waterPositionIndex;
    }

    public function setWaterPositionIndex(int $waterPositionIndex): self
    {
        if ($waterPositionIndex < -1 || $waterPositionIndex >= self::WATER_POSITION_INDICES_TOTAL) {
            throw new \InvalidArgumentException('Water XZ index must be in range -1 ... ' . (self::WATER_POSITION_INDICES_TOTAL - 1));
        }
        $this->waterPositionIndex = $waterPositionIndex;
        return $this;
    }

    public function onNearbyBlockChange(): void {}

    public function onRandomTick(): void {}

    public function ticksRandomly(): bool
    {
        return true;
    }

    public function onEntityLand(mixed ...$args): ?float
    {
        return null;
    }
}
