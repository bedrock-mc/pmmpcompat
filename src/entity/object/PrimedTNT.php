<?php

declare(strict_types=1);

namespace pocketmine\entity\object;

use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;

class PrimedTNT extends Entity
{
    private int $fuse = 80;
    private bool $worksUnderwater = false;
    private bool $exploded = false;

    public function attack(EntityDamageEvent $source): void { parent::attack($source); }
    public function canCollideWith(mixed ...$args): bool { return false; }
    public function explode(mixed ...$args): void
    {
        $this->exploded = true;
        $this->flagForDespawn();
    }
    public function getFuse(mixed ...$args): int { return $this->fuse; }
    public static function getNetworkTypeId(mixed ...$args): string { return 'minecraft:tnt'; }
    public function getOffsetPosition(mixed ...$args): Vector3
    {
        $vector = ($args[0] ?? null) instanceof Vector3 ? $args[0] : ($this->getPosition() ?? Vector3::zero());
        return $vector->add(0, 0.49, 0);
    }
    public function getPickedItem(mixed ...$args): ?Item { return new Item('minecraft:tnt', 'TNT'); }
    public function saveNBT(mixed ...$args): CompoundTag
    {
        $nbt = parent::saveNBT();
        $nbt->setShort('Fuse', $this->fuse);
        $nbt->setByte('WorksUnderwater', $this->worksUnderwater ? 1 : 0);
        return $nbt;
    }
    public function setFuse(mixed ...$args): void
    {
        $fuse = (int) ($args[0] ?? 0);
        if ($fuse < 0 || $fuse > 32767) {
            throw new \InvalidArgumentException('Fuse must be in the range 0-32767');
        }
        $this->fuse = $fuse;
    }
    public function setWorksUnderwater(mixed ...$args): void { $this->worksUnderwater = (bool) ($args[0] ?? true); }
    public function worksUnderwater(mixed ...$args): bool { return $this->worksUnderwater; }
    public function hasExploded(): bool { return $this->exploded; }
}
