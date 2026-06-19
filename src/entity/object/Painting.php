<?php

declare(strict_types=1);

namespace pocketmine\entity\object;

use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;

class Painting extends Entity
{
    public const DATA_TO_FACING = [0 => 3, 1 => 4, 2 => 2, 3 => 5];
    public const TAG_DIRECTION_BE = 'Direction';
    public const TAG_FACING_JE = 'Facing';
    public const TAG_MOTIVE = 'Motive';
    public const TAG_TILE_X = 'TileX';
    public const TAG_TILE_Y = 'TileY';
    public const TAG_TILE_Z = 'TileZ';

    private Vector3 $blockIn;
    private int $facing = 2;
    private PaintingMotive $motive;

    public function __construct(mixed ...$args)
    {
        $location = null;
        $nbt = null;
        $this->blockIn = Vector3::zero();
        $this->motive = PaintingMotive::getMotiveByName('Alban') ?? new PaintingMotive(1, 1, 'Alban');
        foreach ($args as $arg) {
            if ($arg instanceof Location && $location === null) {
                $location = $arg;
            } elseif ($arg instanceof Vector3) {
                $this->blockIn = $arg;
            } elseif (is_int($arg)) {
                $this->facing = $arg;
            } elseif ($arg instanceof PaintingMotive) {
                $this->motive = $arg;
            } elseif ($arg instanceof CompoundTag && $nbt === null) {
                $nbt = $arg;
            }
        }
        parent::__construct($location, $nbt);
    }

    public function canBeCollidedWith(mixed ...$args): bool { return false; }
    public static function canFit(mixed ...$args): bool { return true; }
    public function getFacing(mixed ...$args): int { return $this->facing; }
    public function getMotive(mixed ...$args): PaintingMotive { return $this->motive; }
    public static function getNetworkTypeId(mixed ...$args): string { return 'minecraft:painting'; }
    public function getPickedItem(mixed ...$args): ?Item { return new Item('minecraft:painting', 'Painting'); }
    public function hasMovementUpdate(mixed ...$args): bool { return false; }
    public function onNearbyBlockChange(mixed ...$args): mixed { return null; }
    public function onRandomUpdate(mixed ...$args): mixed { return null; }
    public function saveNBT(mixed ...$args): CompoundTag
    {
        $nbt = parent::saveNBT();
        $nbt->setInt(self::TAG_TILE_X, (int) floor($this->blockIn->x));
        $nbt->setInt(self::TAG_TILE_Y, (int) floor($this->blockIn->y));
        $nbt->setInt(self::TAG_TILE_Z, (int) floor($this->blockIn->z));
        $nbt->setByte(self::TAG_FACING_JE, $this->facing);
        $nbt->setByte(self::TAG_DIRECTION_BE, $this->facing);
        $nbt->setString(self::TAG_MOTIVE, $this->motive->getName());
        return $nbt;
    }
}
