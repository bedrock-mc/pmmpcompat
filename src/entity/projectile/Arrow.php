<?php

declare(strict_types=1);

namespace pocketmine\entity\projectile;

use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\nbt\tag\CompoundTag;

class Arrow extends Projectile
{
    public const PICKUP_ANY = 1;
    public const PICKUP_CREATIVE = 2;
    public const PICKUP_NONE = 0;
    public const TAG_CRIT = 'crit';

    private int $pickupMode = self::PICKUP_ANY;
    private float $punchKnockback = 0.0;
    private bool $critical = false;
    private int $collideTicks = 0;

    public function __construct(mixed ...$args)
    {
        $location = null;
        $owner = null;
        $nbt = null;
        $critical = false;
        foreach ($args as $arg) {
            if ($arg instanceof Location && $location === null) {
                $location = $arg;
            } elseif ($arg instanceof Entity && $owner === null) {
                $owner = $arg;
            } elseif ($arg instanceof CompoundTag && $nbt === null) {
                $nbt = $arg;
            } elseif (is_bool($arg)) {
                $critical = $arg;
            }
        }

        parent::__construct($location, $owner, $nbt);
        $this->setBaseDamage(2.0);
        $this->critical = $critical;
        if ($nbt !== null) {
            $this->pickupMode = $nbt->getByte('pickup', self::PICKUP_ANY);
            $this->critical = $nbt->getByte(self::TAG_CRIT, $this->critical ? 1 : 0) === 1;
            $this->collideTicks = $nbt->getShort('life', 0);
        }
    }

    public static function getNetworkTypeId(mixed ...$args): string { return 'minecraft:arrow'; }
    public function getPickupMode(mixed ...$args): int { return $this->pickupMode; }
    public function getPunchKnockback(mixed ...$args): float { return $this->punchKnockback; }
    public function getResultDamage(mixed ...$args): int { return $this->critical ? parent::getResultDamage() + 1 : parent::getResultDamage(); }
    public function isCritical(mixed ...$args): bool { return $this->critical; }
    public function onCollideWithPlayer(mixed ...$args): mixed { return null; }
    public function saveNBT(mixed ...$args): CompoundTag
    {
        $nbt = parent::saveNBT();
        $nbt->setByte('pickup', $this->pickupMode);
        $nbt->setByte(self::TAG_CRIT, $this->critical ? 1 : 0);
        $nbt->setShort('life', $this->collideTicks);
        return $nbt;
    }
    public function setCritical(mixed ...$args): void { $this->critical = (bool) ($args[0] ?? true); }
    public function setPickupMode(mixed ...$args): void { $this->pickupMode = (int) ($args[0] ?? self::PICKUP_ANY); }
    public function setPunchKnockback(mixed ...$args): void { $this->punchKnockback = max(0.0, (float) ($args[0] ?? 0.0)); }
}
