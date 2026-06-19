<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\item;

use pocketmine\block\Block;
use pocketmine\item\Item;

class ItemSerializerDeserializerRegistrar
{
    public function __construct(
        public ItemDeserializer $deserializer = new ItemDeserializer(),
        public ItemSerializer $serializer = new ItemSerializer()
    ) {}

    public function map1ToNItem(Item $item, string $id, \Closure $deserialize, \Closure $serialize): void
    {
        $this->deserializer->map($id, $deserialize);
        $this->serializer->map($item, $serialize);
    }

    public function map1to1Block(Block $block, string $id): void
    {
        $this->deserializer->mapBlock($id, $block);
        $this->serializer->mapBlock($block, $id);
    }

    public function map1to1BlockWithMeta(Block $block, string $id, int $meta): void
    {
        $this->deserializer->mapBlock($id, $block);
        $this->serializer->map($block->asItem(), new SavedItemData($id, $meta));
    }

    public function map1to1Item(Item $item, string $id): void
    {
        $this->deserializer->map($id, $item);
        $this->serializer->map($item, $id);
    }

    public function map1to1ItemWithMeta(Item $item, string $id, int $meta): void
    {
        $this->deserializer->map($id, $item);
        $this->serializer->map($item, new SavedItemData($id, $meta));
    }
}
