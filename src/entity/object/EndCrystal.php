<?php

declare(strict_types=1);

namespace pocketmine\entity\object;

use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;

class EndCrystal extends Entity
{
    private bool $showBase = false;
    private ?Vector3 $beamTarget = null;
    private bool $exploded = false;

    public function attack(mixed ...$args): mixed { return null; }
    public function explode(mixed ...$args): void
    {
        $this->exploded = true;
        $this->flagForDespawn();
    }
    public function getBeamTarget(mixed ...$args): ?Vector3 { return $this->beamTarget; }
    public static function getNetworkTypeId(mixed ...$args): string { return 'minecraft:ender_crystal'; }
    public function getPickedItem(mixed ...$args): ?Item { return new Item('minecraft:end_crystal', 'End Crystal'); }
    public function isFireProof(mixed ...$args): bool { return true; }
    public function saveNBT(mixed ...$args): CompoundTag
    {
        $nbt = parent::saveNBT();
        $nbt->setByte('ShowBottom', $this->showBase ? 1 : 0);
        if ($this->beamTarget !== null) {
            $nbt->setInt('BlockTargetX', (int) floor($this->beamTarget->x));
            $nbt->setInt('BlockTargetY', (int) floor($this->beamTarget->y));
            $nbt->setInt('BlockTargetZ', (int) floor($this->beamTarget->z));
        }
        return $nbt;
    }
    public function setBeamTarget(mixed ...$args): void { $this->beamTarget = ($args[0] ?? null) instanceof Vector3 ? $args[0] : null; }
    public function setShowBase(mixed ...$args): void { $this->showBase = (bool) ($args[0] ?? true); }
    public function showBase(mixed ...$args): bool { return $this->showBase; }
    public function hasExploded(): bool { return $this->exploded; }
}
