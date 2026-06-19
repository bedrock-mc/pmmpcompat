<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block\convert;

use pocketmine\block\Block;
use pocketmine\block\Slab;
use pocketmine\block\Stair;
use pocketmine\block\Wood;
use pocketmine\data\bedrock\block\BlockStateData;
use pocketmine\data\bedrock\block\BlockStateSerializeException;
use pocketmine\data\bedrock\block\BlockStateSerializer;
use pocketmine\data\bedrock\block\convert\BlockStateSerializerHelper as Helper;

class BlockObjectToStateSerializer implements BlockStateSerializer
{
    /** @var array<string, \Closure|BlockStateData> */
    private array $serializers = [];
    /** @var array<int, Block> */
    private array $stateBlocks = [];
    /** @var array<int, BlockStateData> */
    private array $cache = [];

    public function serialize(int $stateId): BlockStateData
    {
        if (isset($this->cache[$stateId])) {
            return $this->cache[$stateId];
        }
        if (!isset($this->stateBlocks[$stateId])) {
            throw new BlockStateSerializeException("No block registered for runtime state ID $stateId");
        }
        return $this->cache[$stateId] = $this->serializeBlock($this->stateBlocks[$stateId]);
    }

    public function isRegistered(Block $block): bool
    {
        return isset($this->serializers[$block->getTypeId()]);
    }

    public function map(Block $block, \Closure|BlockStateWriter|BlockStateData $serializer): void
    {
        $typeId = $block->getTypeId();
        if (isset($this->serializers[$typeId])) {
            throw new \InvalidArgumentException("Block type ID $typeId (" . $block->getName() . ") already has a serializer registered");
        }
        $this->serializers[$typeId] = $serializer instanceof BlockStateWriter ? $serializer->getBlockStateData() : $serializer;
        $this->stateBlocks[$block->getStateId()] = clone $block;
        $this->cache = [];
    }

    public function mapSimple(Block $block, string $id): void
    {
        $this->map($block, BlockStateData::current($id, []));
    }

    public function mapSlab(Slab $block, string $singleId, string $doubleId): void
    {
        $this->map($block, fn(Slab $block): BlockStateWriter|BlockStateData => Helper::encodeSlab($block, $singleId, $doubleId));
    }

    public function mapStairs(Stair $block, string $id): void
    {
        $this->map($block, fn(Stair $block): BlockStateWriter|BlockStateData => Helper::encodeStairs($block, BlockStateWriter::create($id)));
    }

    public function mapLog(Wood $block, string $unstrippedId, string $strippedId): void
    {
        $this->map($block, fn(Wood $block): BlockStateWriter|BlockStateData => Helper::encodeLog($block, $unstrippedId, $strippedId));
    }

    public function serializeBlock(Block $blockState): BlockStateData
    {
        $typeId = $blockState->getTypeId();
        $serializer = $this->serializers[$typeId] ?? null;
        if ($serializer === null) {
            throw new BlockStateSerializeException("No serializer registered for " . $blockState::class . " with type ID $typeId");
        }
        if ($serializer instanceof BlockStateData) {
            return $serializer;
        }
        $result = $serializer($blockState);
        return $result instanceof BlockStateWriter ? $result->getBlockStateData() : $result;
    }
}
