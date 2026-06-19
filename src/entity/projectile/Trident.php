<?php

declare(strict_types=1);

namespace pocketmine\entity\projectile;

use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;

class Trident extends Projectile
{
    public const TAG_ITEM = 'Trident';

    private Item $item;
    private bool $canCollide = true;

    public function __construct(mixed ...$args)
    {
        $location = null;
        $owner = null;
        $nbt = null;
        $item = null;
        foreach ($args as $arg) {
            if ($arg instanceof Location && $location === null) {
                $location = $arg;
            } elseif ($arg instanceof Item && $item === null) {
                $item = $arg;
            } elseif ($arg instanceof Entity && $owner === null) {
                $owner = $arg;
            } elseif ($arg instanceof CompoundTag && $nbt === null) {
                $nbt = $arg;
            }
        }
        $this->item = clone ($item ?? new Item('minecraft:trident', 'Trident'));
        parent::__construct($location, $owner, $nbt);
        $this->setBaseDamage(8.0);
    }

    public function canCollideWith(mixed ...$args): bool
    {
        $entity = $args[0] ?? null;
        return $this->canCollide && !($entity instanceof Entity && $entity->getId() === $this->getOwningEntityId());
    }

    public function getItem(mixed ...$args): Item { return clone $this->item; }
    public static function getNetworkTypeId(mixed ...$args): string { return 'minecraft:thrown_trident'; }
    public function onCollideWithPlayer(mixed ...$args): mixed { return null; }
    public function saveNBT(mixed ...$args): CompoundTag
    {
        $nbt = parent::saveNBT();
        $nbt->setTag(self::TAG_ITEM, new CompoundTag());
        return $nbt;
    }
    public function setCanCollide(bool $canCollide): void { $this->canCollide = $canCollide; }
    public function setItem(mixed ...$args): void
    {
        if (($args[0] ?? null) instanceof Item) {
            if ($args[0]->isNull()) {
                throw new \InvalidArgumentException('Trident must have a count of at least 1');
            }
            $this->item = clone $args[0];
        }
    }
}
