<?php

declare(strict_types=1);

namespace pocketmine\entity\projectile;

use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\item\PotionType;
use pocketmine\nbt\tag\CompoundTag;

class SplashPotion extends Throwable
{
    public const TAG_POTION_ID = 'PotionId';

    private PotionType $potionType;
    private bool $linger = false;
    private bool $hit = false;

    public function __construct(mixed ...$args)
    {
        $location = null;
        $owner = null;
        $nbt = null;
        $potionType = PotionType::STUB;
        foreach ($args as $arg) {
            if ($arg instanceof Location && $location === null) {
                $location = $arg;
            } elseif ($arg instanceof Entity && $owner === null) {
                $owner = $arg;
            } elseif ($arg instanceof PotionType) {
                $potionType = $arg;
            } elseif ($arg instanceof CompoundTag && $nbt === null) {
                $nbt = $arg;
            }
        }
        $this->potionType = $potionType;
        parent::__construct($location, $owner, $nbt);
    }

    public static function getNetworkTypeId(mixed ...$args): string { return 'minecraft:splash_potion'; }
    public function getPotionEffects(mixed ...$args): array { return []; }
    public function getPotionType(mixed ...$args): PotionType { return $this->potionType; }
    public function getResultDamage(mixed ...$args): int { return -1; }
    public function onHit(mixed ...$args): void
    {
        $this->hit = true;
        $this->flagForDespawn();
    }
    public function saveNBT(mixed ...$args): CompoundTag
    {
        $nbt = parent::saveNBT();
        $nbt->setShort(self::TAG_POTION_ID, 0);
        return $nbt;
    }
    public function setLinger(mixed ...$args): void { $this->linger = (bool) ($args[0] ?? true); }
    public function setPotionType(mixed ...$args): void { if (($args[0] ?? null) instanceof PotionType) { $this->potionType = $args[0]; } }
    public function willLinger(mixed ...$args): bool { return $this->linger; }
    public function hasHit(): bool { return $this->hit; }
}
