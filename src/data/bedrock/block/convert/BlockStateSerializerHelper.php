<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block\convert;

use pocketmine\block\Block;
use pocketmine\block\Liquid;

class BlockStateSerializerHelper
{
    public static function encodeButton(Block $block, BlockStateWriter $out): BlockStateWriter { return $out->writeBool('button_pressed_bit', self::bool($block, 'isPressed')); }
    public static function encodeCandle(Block $block, BlockStateWriter $out): BlockStateWriter { return $out->writeInt('candles', max(0, self::int($block, 'getCount')))->writeBool('lit', self::bool($block, 'isLit')); }
    public static function encodeCauldron(Block $block, BlockStateWriter $out): BlockStateWriter { return $out->writeInt('fill_level', self::int($block, 'getFillLevel')); }
    public static function encodeChemistryTable(Block $block, BlockStateWriter $out): BlockStateWriter { return $out; }
    public static function encodeCrops(Block $block, BlockStateWriter $out): BlockStateWriter { return $out->writeInt('growth', self::int($block, 'getAge')); }
    public static function encodeDoor(Block $block, BlockStateWriter $out): BlockStateWriter { return $out->writeBool('open_bit', self::bool($block, 'isOpen'))->writeBool('upper_block_bit', self::bool($block, 'isTop')); }
    public static function encodeDoublePlant(Block $block, BlockStateWriter $out): BlockStateWriter { return $out->writeBool('upper_block_bit', self::bool($block, 'isTop')); }
    public static function encodeFenceGate(Block $block, BlockStateWriter $out): BlockStateWriter { return $out->writeBool('open_bit', self::bool($block, 'isOpen'))->writeInt('direction', self::int($block, 'getFacing')); }
    public static function encodeFloorSign(Block $block, BlockStateWriter $out): BlockStateWriter { return $out->writeInt('ground_sign_direction', self::int($block, 'getRotation')); }
    public static function encodeFurnace(Block $block, BlockStateWriter $out): BlockStateWriter { return $out->writeInt('facing_direction', self::int($block, 'getFacing')); }
    public static function encodeItemFrame(Block $block, BlockStateWriter $out): BlockStateWriter { return $out->writeBool('item_frame_map_bit', self::bool($block, 'hasMap')); }
    public static function encodeLeaves(Block $block, BlockStateWriter $out): BlockStateWriter { return $out->writeBool('persistent_bit', self::bool($block, 'isPersistent')); }
    public static function encodeLiquid(Liquid $block, string $stillId, string $flowingId): BlockStateWriter { return BlockStateWriter::create($block->isStill() ? $stillId : $flowingId)->writeInt('liquid_depth', $block->getDecay() | ($block->isFalling() ? 0x08 : 0)); }
    public static function encodeLog(Block $block, string $unstrippedId, string $strippedId): BlockStateWriter { return BlockStateWriter::create(self::bool($block, 'isStripped') ? $strippedId : $unstrippedId)->writePillarAxis(self::string($block, 'getAxis', 'y')); }
    public static function encodeMushroomBlock(Block $block, BlockStateWriter $out): BlockStateWriter { return $out->writeInt('huge_mushroom_bits', self::int($block, 'getFaces')); }
    public static function encodeQuartz(Block $block, BlockStateWriter $out): BlockStateWriter { return $out->writeString('chisel_type', self::string($block, 'getType', 'default')); }
    public static function encodeSapling(Block $block, BlockStateWriter $out): BlockStateWriter { return $out->writeInt('age_bit', self::int($block, 'getAge')); }
    public static function encodeSimplePressurePlate(Block $block, BlockStateWriter $out): BlockStateWriter { return $out->writeBool('redstone_signal', self::bool($block, 'isPressed')); }
    public static function encodeSlab(Block $block, string $singleId, string $doubleId): BlockStateWriter { return BlockStateWriter::create(self::string($block, 'getSlabType') === 'double' ? $doubleId : $singleId)->writeSlabPosition(self::bool($block, 'isTop') ? 'top' : 'bottom'); }
    public static function encodeStairs(Block $block, BlockStateWriter $out): BlockStateWriter { return $out->writeBool('upside_down_bit', self::bool($block, 'isUpsideDown'))->writeInt('weirdo_direction', self::int($block, 'getFacing')); }
    public static function encodeStem(Block $block, BlockStateWriter $out): BlockStateWriter { return $out->writeInt('facing_direction', self::int($block, 'getFacing')); }
    public static function encodeTorch(Block $block, BlockStateWriter $out): BlockStateWriter { return $out->writeTorchFacing(self::string($block, 'getFacing', 'top')); }
    public static function encodeTrapdoor(Block $block, BlockStateWriter $out): BlockStateWriter { return $out->writeBool('open_bit', self::bool($block, 'isOpen'))->writeBool('upside_down_bit', self::bool($block, 'isTop')); }
    public static function encodeWall(Block $block, BlockStateWriter $out): BlockStateWriter { return $out->writeBool('wall_post_bit', self::bool($block, 'hasPost')); }
    public static function encodeWallSign(Block $block, BlockStateWriter $out): BlockStateWriter { return $out->writeInt('facing_direction', self::int($block, 'getFacing')); }
    public static function selectCopperId(string $normalId, string $exposedId, string $weatheredId, string $oxidizedId, mixed $oxidation = null): string
    {
        return match ((string) ($oxidation instanceof \BackedEnum ? $oxidation->value : ($oxidation instanceof \UnitEnum ? $oxidation->name : $oxidation))) {
            'exposed', '1' => $exposedId,
            'weathered', '2' => $weatheredId,
            'oxidized', '3' => $oxidizedId,
            default => $normalId,
        };
    }

    private static function bool(Block $block, string $method): bool { return method_exists($block, $method) ? (bool) $block->{$method}() : false; }
    private static function int(Block $block, string $method): int { return method_exists($block, $method) ? (int) $block->{$method}() : 0; }
    private static function string(Block $block, string $method, string $default = ''): string
    {
        if (!method_exists($block, $method)) {
            return $default;
        }
        $value = $block->{$method}();
        return $value instanceof \BackedEnum ? (string) $value->value : ($value instanceof \UnitEnum ? $value->name : (string) $value);
    }
}
