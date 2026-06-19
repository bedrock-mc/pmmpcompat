<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\item;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;

final class SavedItemStackData
{
    public const TAG_COUNT = 'Count';
    public const TAG_SLOT = 'Slot';
    public const TAG_WAS_PICKED_UP = 'WasPickedUp';
    public const TAG_CAN_PLACE_ON = 'CanPlaceOn';
    public const TAG_CAN_DESTROY = 'CanDestroy';

    /**
     * @param string[] $canPlaceOn
     * @param string[] $canDestroy
     */
    public function __construct(
        private SavedItemData $typeData,
        private int $count,
        private ?int $slot,
        private ?bool $wasPickedUp,
        private array $canPlaceOn,
        private array $canDestroy
    ) {}

    public function getTypeData(): SavedItemData
    {
        return $this->typeData;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getSlot(): ?int
    {
        return $this->slot;
    }

    public function getWasPickedUp(): ?bool
    {
        return $this->wasPickedUp;
    }

    /**
     * @return string[]
     */
    public function getCanPlaceOn(): array
    {
        return $this->canPlaceOn;
    }

    /**
     * @return string[]
     */
    public function getCanDestroy(): array
    {
        return $this->canDestroy;
    }

    public function toNbt(): CompoundTag
    {
        $result = $this->typeData->toNbt()
            ->setByte(self::TAG_COUNT, self::signedByte($this->count));

        if ($this->slot !== null) {
            $result->setByte(self::TAG_SLOT, self::signedByte($this->slot));
        }
        if ($this->wasPickedUp !== null) {
            $result->setByte(self::TAG_WAS_PICKED_UP, $this->wasPickedUp ? 1 : 0);
        }
        if ($this->canPlaceOn !== []) {
            $result->setTag(self::TAG_CAN_PLACE_ON, self::stringListTag($this->canPlaceOn));
        }
        if ($this->canDestroy !== []) {
            $result->setTag(self::TAG_CAN_DESTROY, self::stringListTag($this->canDestroy));
        }

        return $result;
    }

    private static function signedByte(int $value): int
    {
        $value &= 0xff;
        return $value >= 0x80 ? $value - 0x100 : $value;
    }

    /**
     * @param string[] $values
     */
    private static function stringListTag(array $values): ListTag
    {
        return new ListTag(array_map(static fn(string $value): StringTag => new StringTag($value), array_values($values)));
    }
}
