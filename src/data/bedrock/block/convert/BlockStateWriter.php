<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block\convert;

use pocketmine\block\utils\WallConnectionType;
use pocketmine\data\bedrock\block\BlockStateData;
use pocketmine\data\bedrock\block\convert\property\IntFromRawStateMap;
use pocketmine\data\bedrock\block\convert\property\WallConnectionTypeShim;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\Tag;

class BlockStateWriter
{
    /** @var array<string, Tag> */
    private array $states = [];

    public function __construct(private string $id) {}
    public static function create(string $id): self { return new self($id); }
    public function getBlockStateData(): BlockStateData { return BlockStateData::current($this->id, $this->states); }
    public function mapIntToInt(string $name, IntFromRawStateMap $map, int $value): self { return $this->writeInt($name, $map->valueToRaw($value)); }
    public function mapIntToString(string $name, IntFromRawStateMap $map, int $value): self { return $this->writeString($name, (string) $map->valueToRaw($value)); }
    public function write5MinusHorizontalFacing(int $value): self { return $this->writeInt('direction', $value); }
    public function writeBellAttachmentType(string|\UnitEnum $value): self { return $this->writeString('attachment', $value instanceof \UnitEnum ? $value->name : $value); }
    public function writeBlockFace(int|string $value): self { return is_int($value) ? $this->writeInt('minecraft:block_face', $value) : $this->writeString('minecraft:block_face', $value); }
    public function writeBool(string $name, bool $value): self { $this->states[$name] = new ByteTag($value ? 1 : 0); return $this; }
    public function writeCardinalHorizontalFacing(string|int $value): self { return is_int($value) ? $this->writeInt('minecraft:cardinal_direction', $value) : $this->writeString('minecraft:cardinal_direction', $value); }
    public function writeCoralFacing(int $value): self { return $this->writeInt('coral_direction', $value); }
    public function writeEndRodFacingDirection(int $value): self { return $this->writeFacingDirection($value); }
    public function writeFacingDirection(int $value): self { return $this->writeInt('facing_direction', $value); }
    /** @param int[] $faces */
    public function writeFacingFlags(array $faces): self
    {
        $flags = 0;
        foreach ($faces as $face) {
            $flags |= 1 << (int) $face;
        }
        return $this->writeInt('multi_face_direction_bits', $flags);
    }
    public function writeFacingWithoutDown(int $value): self { return $this->writeFacingDirection($value); }
    public function writeFacingWithoutUp(int $value): self { return $this->writeFacingDirection($value); }
    public function writeHorizontalFacing(int $value): self { return $this->writeFacingDirection($value); }
    public function writeInt(string $name, int $value): self { $this->states[$name] = new IntTag($value); return $this; }
    public function writeLegacyHorizontalFacing(int $value): self { return $this->writeInt('direction', $value); }
    public function writePillarAxis(int|string $value): self { return is_int($value) ? $this->writeInt('pillar_axis', $value) : $this->writeString('pillar_axis', $value); }
    public function writeSlabPosition(string $value): self { return $this->writeString('minecraft:vertical_half', $value); }
    public function writeString(string $name, string $value): self { $this->states[$name] = new StringTag($value); return $this; }
    public function writeTorchFacing(string $value): self { return $this->writeString('torch_facing_direction', $value); }
    public function writeUnitEnum(string $name, \UnitEnum $value): self { return $this->writeString($name, $value instanceof \BackedEnum ? (string) $value->value : $value->name); }
    public function writeWallConnectionType(string $name, ?WallConnectionType $value): self { return $this->writeString($name, WallConnectionTypeShim::serialize($value)->getValue()); }
    public function writeWeirdoHorizontalFacing(int $value): self { return $this->writeInt('weirdo_direction', $value); }
}
