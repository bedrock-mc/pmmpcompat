<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\item;

use pocketmine\block\Block;
use pocketmine\item\Item;

class ItemSerializer
{
    /** @var array<string, \Closure|SavedItemData|string> */
    private array $serializers = [];

    public function __construct(private ?BlockItemIdMap $blockItemIdMap = null)
    {
        $this->blockItemIdMap ??= BlockItemIdMap::getInstance();
    }

    public function map(Item $item, \Closure|SavedItemData|string $serializer): void
    {
        $this->serializers[$item->getTypeId()] = $serializer;
    }

    public function mapBlock(Block $block, ?string $itemId = null): void
    {
        $this->serializers[$block->asItem()->getTypeId()] = $itemId ?? $this->blockItemIdMap?->lookupItemId($block->getTypeId()) ?? $block->getTypeId();
    }

    public function serializeType(Item $item): SavedItemData
    {
        $serializer = $this->serializers[$item->getTypeId()] ?? $item->getTypeId();
        $result = $serializer instanceof \Closure ? $serializer($item) : $serializer;
        if ($result instanceof SavedItemData) {
            return $result;
        }
        if (is_string($result)) {
            return new SavedItemData($result);
        }
        throw new ItemTypeSerializeException('Item serializer for ' . $item->getTypeId() . ' returned ' . get_debug_type($result));
    }

    public function serializeStack(Item $item, ?int $slot = null): SavedItemStackData
    {
        return new SavedItemStackData(
            $this->serializeType($item),
            $item->getCount(),
            $slot,
            null,
            $item->getCanPlaceOn(),
            $item->getCanDestroy()
        );
    }
}
