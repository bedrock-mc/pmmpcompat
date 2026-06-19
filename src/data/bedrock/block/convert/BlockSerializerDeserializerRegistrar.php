<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block\convert;

use pocketmine\block\Block;
use pocketmine\block\Slab;
use pocketmine\block\Stair;
use pocketmine\data\bedrock\block\BlockStateData;

class BlockSerializerDeserializerRegistrar
{
    public function __construct(
        public BlockStateToObjectDeserializer $deserializer,
        public BlockObjectToStateSerializer $serializer
    ) {}

    public function mapSimple(Block $block, string $id): void
    {
        $this->serializer->mapSimple($block, $id);
        $this->deserializer->mapSimple($id, static fn(): Block => clone $block);
    }

    public function mapModel(Model $model): void
    {
        $block = $model->getBlock();
        $id = $model->getId();
        $this->serializer->map($block, BlockStateData::current($id));
        $this->deserializer->mapSimple($id, static fn(): Block => clone $block);
    }

    public function mapFlattenedId(FlattenedIdModel $model): void
    {
        $block = $model->getBlock();
        $id = $this->flattenedId($model);
        $this->serializer->map($block, BlockStateData::current($id));
        $this->deserializer->mapSimple($id, static fn(): Block => clone $block);
    }

    public function mapSlab(Slab $block, string $singleId, string $doubleId): void
    {
        $this->serializer->mapSlab($block, $singleId, $doubleId);
        $this->deserializer->mapSlab($singleId, $doubleId, static fn(): Slab => clone $block);
    }

    public function mapStairs(Stair $block, string $id): void
    {
        $this->serializer->mapStairs($block, $id);
        $this->deserializer->mapStairs($id, static fn(): Stair => clone $block);
    }

    public function mapColored(string $prefix, string $suffix, \Closure $getBlock): void
    {
        foreach (['white', 'orange', 'magenta', 'light_blue', 'yellow', 'lime', 'pink', 'gray', 'silver', 'cyan', 'purple', 'blue', 'brown', 'green', 'red', 'black'] as $color) {
            $block = $getBlock($color);
            if ($block instanceof Block) {
                $this->mapSimple($block, $prefix . $color . $suffix);
            }
        }
    }

    private function flattenedId(FlattenedIdModel $model): string
    {
        $parts = [];
        foreach ($model->getIdComponents() as $component) {
            if (is_string($component)) {
                $parts[] = $component;
            }
        }
        return $parts === [] ? $model->getBlock()->getTypeId() : implode('', $parts);
    }
}
