<?php

declare(strict_types=1);

namespace pocketmine\entity\object;

use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;

class ItemEntity extends Entity
{
    public const TAG_ITEM = 'Item';
    public const MERGE_CHECK_PERIOD = 2;
    public const DEFAULT_DESPAWN_DELAY = 6000;
    public const NEVER_DESPAWN = -1;
    public const MAX_DESPAWN_DELAY = 32767 + self::DEFAULT_DESPAWN_DELAY;

    private string $owner = '';
    private string $thrower = '';
    private int $pickupDelay = 0;
    private int $despawnDelay = self::DEFAULT_DESPAWN_DELAY;
    private Item $item;

    public function __construct(mixed ...$args)
    {
        $location = null;
        $nbt = null;
        $item = null;
        foreach ($args as $arg) {
            if ($arg instanceof Location && $location === null) {
                $location = $arg;
            } elseif ($arg instanceof CompoundTag && $nbt === null) {
                $nbt = $arg;
            } elseif ($arg instanceof Item) {
                $item = $arg;
            }
        }
        $this->item = clone ($item ?? VanillaItems::AIR());
        parent::__construct($location, $nbt);
    }

    public static function getNetworkTypeId(mixed ...$args): string
    {
        return 'minecraft:item';
    }

    public function getItem(): Item
    {
        return clone $this->item;
    }

    public function setStackSize(int $size): void
    {
        $this->item = (clone $this->item)->setCount($size);
    }

    public function getOwner(): string
    {
        return $this->owner;
    }

    public function setOwner(string $owner): void
    {
        $this->owner = $owner;
    }

    public function getThrower(): string
    {
        return $this->thrower;
    }

    public function setThrower(string $thrower): void
    {
        $this->thrower = $thrower;
    }

    public function getPickupDelay(): int
    {
        return $this->pickupDelay;
    }

    public function setPickupDelay(int $pickupDelay): void
    {
        if ($pickupDelay < 0 && $pickupDelay !== self::NEVER_DESPAWN) {
            throw new \InvalidArgumentException('Pickup delay must be non-negative or ' . self::NEVER_DESPAWN);
        }
        $this->pickupDelay = $pickupDelay;
    }

    public function getDespawnDelay(): int
    {
        return $this->despawnDelay;
    }

    public function setDespawnDelay(int $despawnDelay): void
    {
        if (($despawnDelay < 0 || $despawnDelay > self::MAX_DESPAWN_DELAY) && $despawnDelay !== self::NEVER_DESPAWN) {
            throw new \InvalidArgumentException('Despawn ticker must be in range 0 ... ' . self::MAX_DESPAWN_DELAY . ' or ' . self::NEVER_DESPAWN);
        }
        $this->despawnDelay = $despawnDelay;
    }

    public function isMergeable(mixed ...$args): bool
    {
        return $this->pickupDelay !== self::NEVER_DESPAWN && $this->item->getCount() < $this->item->getMaxStackSize();
    }

    public function tryMergeInto(mixed ...$args): bool
    {
        return false;
    }

    public function canBeCollidedWith(mixed ...$args): bool { return false; }
    public function canCollideWith(mixed ...$args): bool { return false; }
    public function canSaveWithChunk(mixed ...$args): bool { return true; }
    public function getOffsetPosition(mixed ...$args): mixed { return $this->getPosition(); }
    public function isFireProof(mixed ...$args): bool { return $this->item->isFireProof(); }
    public function onCollideWithPlayer(mixed ...$args): mixed { return null; }
    public function saveNBT(mixed ...$args): CompoundTag { return parent::saveNBT(); }
}
