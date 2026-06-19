<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block\convert;

use pocketmine\block\Block;
use pocketmine\block\Liquid;

class BlockStateDeserializerHelper
{
    public static function decodeButton(Block $block, BlockStateReader $in): Block { self::set($block, 'setPressed', $in->readBool('button_pressed_bit')); return $block; }
    public static function decodeCandle(Block $block, BlockStateReader $in): Block { self::set($block, 'setCount', $in->readInt('candles')); self::set($block, 'setLit', $in->readBool('lit')); return $block; }
    public static function decodeComparator(Block $block, BlockStateReader $in): Block { self::set($block, 'setFacing', $in->readInt('direction')); self::set($block, 'setPowered', $in->readBool('output_lit_bit')); return $block; }
    public static function decodeCopper(Block $block, mixed ...$args): Block { return $block; }
    public static function decodeCrops(Block $block, BlockStateReader $in): Block { self::set($block, 'setAge', $in->readInt('growth')); return $block; }
    public static function decodeDaylightSensor(Block $block, BlockStateReader $in): Block { self::set($block, 'setInverted', $in->readBool('inverted_bit')); return $block; }
    public static function decodeDoor(Block $block, BlockStateReader $in): Block { self::set($block, 'setOpen', $in->readBool('open_bit')); self::set($block, 'setTop', $in->readBool('upper_block_bit')); return $block; }
    public static function decodeDoublePlant(Block $block, BlockStateReader $in): Block { self::set($block, 'setTop', $in->readBool('upper_block_bit')); return $block; }
    public static function decodeDoubleSlab(Block $block, BlockStateReader $in): Block { self::set($block, 'setSlabType', 'double'); return $block; }
    public static function decodeFenceGate(Block $block, BlockStateReader $in): Block { self::set($block, 'setOpen', $in->readBool('open_bit')); self::set($block, 'setFacing', $in->readInt('direction')); return $block; }
    public static function decodeFloorCoralFan(Block $block, BlockStateReader $in): Block { self::set($block, 'setFacing', $in->readInt('coral_fan_direction')); return $block; }
    public static function decodeFloorSign(Block $block, BlockStateReader $in): Block { self::set($block, 'setRotation', $in->readInt('ground_sign_direction')); return $block; }
    public static function decodeFlowingLiquid(Liquid $block, BlockStateReader $in): Liquid { return self::decodeLiquid($block->setStill(false), $in); }
    public static function decodeItemFrame(Block $block, BlockStateReader $in): Block { self::set($block, 'setMap', $in->readBool('item_frame_map_bit')); return $block; }
    public static function decodeLeaves(Block $block, BlockStateReader $in): Block { self::set($block, 'setPersistent', $in->readBool('persistent_bit')); return $block; }
    public static function decodeLiquid(Liquid $block, BlockStateReader $in): Liquid
    {
        $depth = $in->readInt('liquid_depth');
        return $block->setDecay($depth & 0x07)->setFalling(($depth & 0x08) !== 0);
    }
    public static function decodeLog(Block $block, bool $stripped, BlockStateReader $in): Block { self::set($block, 'setStripped', $stripped); self::set($block, 'setAxis', $in->readPillarAxis()); return $block; }
    public static function decodeMushroomBlock(Block $block, BlockStateReader $in): Block { self::set($block, 'setFaces', $in->readInt('huge_mushroom_bits')); return $block; }
    public static function decodeRepeater(Block $block, BlockStateReader $in): Block { self::set($block, 'setDelay', $in->readInt('repeater_delay')); self::set($block, 'setFacing', $in->readInt('direction')); return $block; }
    public static function decodeSapling(Block $block, BlockStateReader $in): Block { self::set($block, 'setAge', $in->readInt('age_bit')); return $block; }
    public static function decodeSimplePressurePlate(Block $block, BlockStateReader $in): Block { self::set($block, 'setPressed', $in->readBool('redstone_signal')); return $block; }
    public static function decodeSingleSlab(Block $block, BlockStateReader $in): Block { self::set($block, 'setSlabType', $in->readSlabPosition()); return $block; }
    public static function decodeStairs(Block $block, BlockStateReader $in): Block { self::set($block, 'setUpsideDown', $in->readBool('upside_down_bit')); self::set($block, 'setFacing', $in->readInt('weirdo_direction')); return $block; }
    public static function decodeStem(Block $block, BlockStateReader $in): Block { self::set($block, 'setFacing', $in->readInt('facing_direction')); return $block; }
    public static function decodeStillLiquid(Liquid $block, BlockStateReader $in): Liquid { return self::decodeLiquid($block->setStill(true), $in); }
    public static function decodeTrapdoor(Block $block, BlockStateReader $in): Block { self::set($block, 'setOpen', $in->readBool('open_bit')); self::set($block, 'setTop', $in->readBool('upside_down_bit')); return $block; }
    public static function decodeWall(Block $block, BlockStateReader $in): Block { self::set($block, 'setPost', $in->readBool('wall_post_bit')); return $block; }
    public static function decodeWallSign(Block $block, BlockStateReader $in): Block { self::set($block, 'setFacing', $in->readInt('facing_direction')); return $block; }
    public static function decodeWaxedCopper(Block $block, mixed ...$args): Block { self::set($block, 'setWaxed', true); return $block; }
    public static function decodeWeightedPressurePlate(Block $block, BlockStateReader $in): Block { self::set($block, 'setSignalStrength', $in->readInt('redstone_signal')); return $block; }

    private static function set(Block $block, string $method, mixed $value): void
    {
        if (method_exists($block, $method)) {
            $block->{$method}($value);
        }
    }
}
