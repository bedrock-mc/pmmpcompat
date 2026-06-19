<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block\convert;

use pocketmine\block\utils\WallConnectionType;
use pocketmine\data\bedrock\block\BlockStateData;
use pocketmine\data\bedrock\block\BlockStateDeserializeException;
use pocketmine\data\bedrock\block\convert\property\IntFromRawStateMap;
use pocketmine\data\bedrock\block\convert\property\WallConnectionTypeShim;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\Tag;

class BlockStateReader
{
    /** @var array<string, Tag> */
    private array $unusedStates;

    public function __construct(private BlockStateData $data)
    {
        $this->unusedStates = $data->getStates();
    }

    public function badValueException(string $name, string $stringifiedValue, ?string $reason = null): BlockStateDeserializeException
    {
        return new BlockStateDeserializeException('Property "' . $name . '" has unexpected value "' . $stringifiedValue . '"' . ($reason !== null ? ' (' . $reason . ')' : ''));
    }
    public function checkUnreadProperties(): void
    {
        if ($this->unusedStates !== []) {
            throw new BlockStateDeserializeException('Unread block state properties: ' . implode(', ', array_keys($this->unusedStates)));
        }
    }
    public function ignored(string $name): void { unset($this->unusedStates[$name]); }
    public function mapIntFromInt(string $name, IntFromRawStateMap $map): int
    {
        $raw = $this->readInt($name);
        return $map->rawToValue($raw) ?? throw $this->badValueException($name, (string) $raw);
    }
    public function mapIntFromString(string $name, IntFromRawStateMap $map): int
    {
        $raw = $this->readString($name);
        return $map->rawToValue($raw) ?? throw $this->badValueException($name, $raw);
    }
    public function missingOrWrongTypeException(string $name, ?Tag $tag): BlockStateDeserializeException
    {
        return new BlockStateDeserializeException('Property "' . $name . '" ' . ($tag === null ? 'is missing' : 'has unexpected type ' . $tag::class));
    }
    public function read5MinusHorizontalFacing(): int { return $this->readInt('direction'); }
    public function readBellAttachmentType(): string { return $this->readString('attachment'); }
    public function readBlockFace(): int|string { return $this->readString('minecraft:block_face'); }
    public function readBool(string $name): bool
    {
        unset($this->unusedStates[$name]);
        $tag = $this->data->getState($name);
        if ($tag instanceof ByteTag) {
            return $tag->getValue() !== 0;
        }
        throw $this->missingOrWrongTypeException($name, $tag);
    }
    public function readBoundedInt(string $name, int $min, int $max): int
    {
        $value = $this->readInt($name);
        if ($value < $min || $value > $max) {
            throw $this->badValueException($name, (string) $value, "Must be inside the range $min ... $max");
        }
        return $value;
    }
    public function readCardinalHorizontalFacing(): string { return $this->readString('minecraft:cardinal_direction'); }
    public function readCoralFacing(): int { return $this->readInt('coral_direction'); }
    public function readEndRodFacingDirection(): int { return $this->readFacingDirection(); }
    public function readFacingDirection(): int { return $this->readInt('facing_direction'); }
    /** @return array<int, int> */
    public function readFacingFlags(): array
    {
        $flags = $this->readInt('multi_face_direction_bits');
        $faces = [];
        foreach ([0 => 1, 1 => 2, 2 => 4, 3 => 8, 4 => 16, 5 => 32] as $face => $flag) {
            if (($flags & $flag) !== 0) {
                $faces[$face] = $face;
            }
        }
        return $faces;
    }
    public function readFacingWithoutDown(): int { return $this->readFacingDirection(); }
    public function readFacingWithoutUp(): int { return $this->readFacingDirection(); }
    public function readHorizontalFacing(): int { return $this->readFacingDirection(); }
    public function readInt(string $name): int
    {
        unset($this->unusedStates[$name]);
        $tag = $this->data->getState($name);
        if ($tag instanceof IntTag) {
            return $tag->getValue();
        }
        throw $this->missingOrWrongTypeException($name, $tag);
    }
    public function readLegacyHorizontalFacing(): int { return $this->readInt('direction'); }
    public function readPillarAxis(): int|string { return $this->readString('pillar_axis'); }
    public function readSlabPosition(): string { return $this->readString('minecraft:vertical_half'); }
    public function readString(string $name): string
    {
        unset($this->unusedStates[$name]);
        $tag = $this->data->getState($name);
        if ($tag instanceof StringTag) {
            return $tag->getValue();
        }
        throw $this->missingOrWrongTypeException($name, $tag);
    }
    public function readTorchFacing(): string { return $this->readString('torch_facing_direction'); }
    public function readUnitEnum(string $name, string $enumClass): \UnitEnum
    {
        $raw = $this->readString($name);
        foreach ($enumClass::cases() as $case) {
            if ($case->name === $raw || ($case instanceof \BackedEnum && (string) $case->value === $raw)) {
                return $case;
            }
        }
        throw $this->badValueException($name, $raw);
    }
    public function readWallConnectionType(string $name = 'wall_connection_type_east'): ?WallConnectionType
    {
        return WallConnectionTypeShim::fromRaw($this->readString($name))->deserialize();
    }
    public function readWeirdoHorizontalFacing(): int { return $this->readInt('weirdo_direction'); }
    public function todo(string $name): void { $this->ignored($name); }
}
