<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\item;

use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;

class ItemDeserializer
{
    /** @var array<string, \Closure|Item> */
    private array $deserializers = [];

    public function __construct(private ?BlockItemIdMap $blockItemIdMap = null)
    {
        $this->blockItemIdMap ??= BlockItemIdMap::getInstance();
    }

    public function map(string $id, \Closure|Item $deserializer): void
    {
        $this->deserializers[$id] = $deserializer;
    }

    public function mapBlock(string $id, Block $block): void
    {
        $this->deserializers[$id] = $block->asItem();
        $itemId = $this->blockItemIdMap?->lookupItemId($block->getTypeId());
        if ($itemId !== null) {
            $this->deserializers[$itemId] = $block->asItem();
        }
    }

    public function getDeserializerForId(string $id): ?\Closure
    {
        $deserializer = $this->deserializers[$id] ?? null;
        if ($deserializer instanceof \Closure) {
            return $deserializer;
        }
        if ($deserializer instanceof Item) {
            return static fn(SavedItemData $data): Item => clone $deserializer;
        }
        return null;
    }

    public function deserializeType(SavedItemData|string|CompoundTag $data): Item
    {
        $saved = $this->normaliseTypeData($data);
        $id = $saved->getName();
        $deserializer = $this->deserializers[$id] ?? null;
        if ($deserializer instanceof \Closure) {
            $item = $deserializer($saved);
            if (!$item instanceof Item) {
                throw new ItemTypeDeserializeException('Item deserializer for ' . $id . ' returned ' . get_debug_type($item));
            }
            return $item;
        }
        if ($deserializer instanceof Item) {
            return clone $deserializer;
        }
        return new Item($id, self::nameFromId($id));
    }

    public function deserializeStack(SavedItemStackData|SavedItemData|CompoundTag|string $data): Item
    {
        if ($data instanceof SavedItemStackData) {
            return $this->deserializeType($data->getTypeData())->setCount($data->getCount());
        }
        if ($data instanceof CompoundTag) {
            return $this->deserializeType($data)->setCount($data->getByte(SavedItemStackData::TAG_COUNT, 1));
        }
        return $this->deserializeType($data);
    }

    private function normaliseTypeData(SavedItemData|string|CompoundTag $data): SavedItemData
    {
        if ($data instanceof SavedItemData) {
            return $data;
        }
        if (is_string($data)) {
            return new SavedItemData($data);
        }
        return new SavedItemData(
            $data->getString(SavedItemData::TAG_NAME, 'minecraft:air'),
            $data->getShort(SavedItemData::TAG_DAMAGE, 0),
            null,
            $data->getCompoundTag(SavedItemData::TAG_TAG)
        );
    }

    private static function nameFromId(string $id): string
    {
        $name = str_starts_with($id, 'minecraft:') ? substr($id, 10) : $id;
        return ucwords(str_replace('_', ' ', $name));
    }
}
