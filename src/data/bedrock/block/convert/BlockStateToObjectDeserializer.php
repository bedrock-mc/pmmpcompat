<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block\convert;

use pocketmine\block\Block;
use pocketmine\block\Slab;
use pocketmine\block\Stair;
use pocketmine\block\Wood;
use pocketmine\data\bedrock\block\BlockStateData;
use pocketmine\data\bedrock\block\BlockStateDeserializer;
use pocketmine\data\bedrock\block\convert\BlockStateDeserializerHelper as Helper;

class BlockStateToObjectDeserializer implements BlockStateDeserializer
{
    /** @var array<string, \Closure> */
    private array $deserializeFuncs = [];
    /** @var array<string, int> */
    private array $simpleCache = [];

    public function deserialize(BlockStateData $stateData): int
    {
        if ($stateData->getStates() === []) {
            return $this->simpleCache[$stateData->getName()] ??= $this->deserializeBlock($stateData)->getStateId();
        }
        return $this->deserializeBlock($stateData)->getStateId();
    }

    public function map(string $id, \Closure $c): void
    {
        $this->deserializeFuncs[$id] = $c;
        $this->simpleCache = [];
    }

    public function getDeserializerForId(string $id): ?\Closure
    {
        return $this->deserializeFuncs[$id] ?? null;
    }

    public function mapSimple(string $id, \Closure $getBlock): void
    {
        $this->map($id, fn(BlockStateReader $in): Block => $getBlock());
    }

    public function mapSlab(string $singleId, string $doubleId, \Closure $getBlock): void
    {
        $this->map($singleId, fn(BlockStateReader $in): Slab => Helper::decodeSingleSlab($getBlock($in), $in));
        $this->map($doubleId, fn(BlockStateReader $in): Slab => Helper::decodeDoubleSlab($getBlock($in), $in));
    }

    public function mapStairs(string $id, \Closure $getBlock): void
    {
        $this->map($id, fn(BlockStateReader $in): Stair => Helper::decodeStairs($getBlock(), $in));
    }

    public function mapLog(string $unstrippedId, string $strippedId, \Closure $getBlock): void
    {
        $this->map($unstrippedId, fn(BlockStateReader $in): Wood => Helper::decodeLog($getBlock(), false, $in));
        $this->map($strippedId, fn(BlockStateReader $in): Wood => Helper::decodeLog($getBlock(), true, $in));
    }

    public function deserializeBlock(BlockStateData $blockStateData): Block
    {
        $id = $blockStateData->getName();
        if (!isset($this->deserializeFuncs[$id])) {
            throw new UnsupportedBlockStateException("Unknown block ID \"$id\"");
        }
        $reader = new BlockStateReader($blockStateData);
        $block = ($this->deserializeFuncs[$id])($reader);
        $reader->checkUnreadProperties();
        return $block;
    }
}
