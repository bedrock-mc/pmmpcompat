<?php

declare(strict_types=1);

namespace pocketmine\entity\projectile;

use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;

class Projectile extends Entity
{
    private float $baseDamage = 0.0;
    private ?Entity $owningEntity = null;
    private ?Vector3 $blockHit = null;

    public function __construct(mixed ...$args)
    {
        $location = null;
        $owner = null;
        $nbt = null;
        foreach ($args as $arg) {
            if ($arg instanceof Location && $location === null) {
                $location = $arg;
            } elseif ($arg instanceof Entity && $owner === null) {
                $owner = $arg;
            } elseif ($arg instanceof CompoundTag && $nbt === null) {
                $nbt = $arg;
            } elseif ($arg instanceof Vector3 && $this->blockHit === null) {
                $this->blockHit = $arg;
            }
        }

        parent::__construct($location, $nbt);
        if ($owner !== null) {
            $this->setOwningEntity($owner);
        }
        if ($nbt !== null) {
            $this->baseDamage = $nbt->getDouble('damage', $this->baseDamage);
            $this->blockHit = $this->readBlockHit($nbt);
        }
    }

    public function getBaseDamage(): float
    {
        return $this->baseDamage;
    }

    public function setBaseDamage(float $damage): void
    {
        $this->baseDamage = $damage;
    }

    public function getResultDamage(): int
    {
        return (int) ceil($this->baseDamage);
    }

    public function getOwningEntity(mixed ...$args): ?Entity
    {
        return $this->owningEntity !== null && !$this->owningEntity->isClosed() ? $this->owningEntity : null;
    }

    public function setOwningEntity(mixed ...$args): mixed
    {
        $this->owningEntity = ($args[0] ?? null) instanceof Entity ? $args[0] : null;
        return parent::setOwningEntity($this->owningEntity);
    }

    public function getBlockHit(): ?Vector3
    {
        return $this->blockHit;
    }

    public function setBlockHit(?Vector3 $blockHit): void
    {
        $this->blockHit = $blockHit;
    }

    public function attack(mixed ...$args): mixed { return null; }
    public function canBeCollidedWith(mixed ...$args): bool { return false; }
    public function canCollideWith(mixed ...$args): bool { return true; }
    public function hasMovementUpdate(mixed ...$args): bool { return $this->blockHit === null; }
    public function onNearbyBlockChange(mixed ...$args): mixed { return null; }
    public function saveNBT(mixed ...$args): CompoundTag
    {
        $nbt = parent::saveNBT();
        $nbt->setDouble('damage', $this->baseDamage);
        if ($this->blockHit !== null) {
            $nbt->setTag('StuckToBlockPos', new ListTag([
                new IntTag((int) floor($this->blockHit->x)),
                new IntTag((int) floor($this->blockHit->y)),
                new IntTag((int) floor($this->blockHit->z)),
            ]));
        }
        return $nbt;
    }

    private function readBlockHit(CompoundTag $nbt): ?Vector3
    {
        $stuck = $nbt->getTag('StuckToBlockPos');
        if ($stuck instanceof ListTag && count($stuck) >= 3) {
            $values = $stuck->getValue();
            return new Vector3(
                (float) (($values[0] ?? null)?->getValue() ?? 0),
                (float) (($values[1] ?? null)?->getValue() ?? 0),
                (float) (($values[2] ?? null)?->getValue() ?? 0),
            );
        }
        if ($nbt->getTag('tileX') !== null || $nbt->getTag('tileY') !== null || $nbt->getTag('tileZ') !== null) {
            return new Vector3($nbt->getInt('tileX'), $nbt->getInt('tileY'), $nbt->getInt('tileZ'));
        }
        return null;
    }
}
