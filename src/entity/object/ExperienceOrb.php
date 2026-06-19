<?php

declare(strict_types=1);

namespace pocketmine\entity\object;

use pocketmine\entity\Entity;
use pocketmine\nbt\tag\CompoundTag;

class ExperienceOrb extends Entity
{
    public const DEFAULT_DESPAWN_DELAY = 6000;
    public const MAX_DESPAWN_DELAY = 38767;
    public const MAX_TARGET_DISTANCE = 8.0;
    public const NEVER_DESPAWN = -1;
    public const ORB_SPLIT_SIZES = [2477, 1237, 617, 307, 149, 73, 37, 17, 7, 3, 1];
    public const TAG_VALUE_PC = 'Value';
    public const TAG_VALUE_PE = 'experience value';

    private int $xpValue = 1;
    private int $despawnDelay = self::DEFAULT_DESPAWN_DELAY;
    private ?Entity $targetPlayer = null;

    public function __construct(mixed ...$args)
    {
        foreach ($args as $arg) {
            if (is_int($arg)) {
                $this->setXpValue($arg);
                break;
            }
        }
        parent::__construct($args[0] instanceof \pocketmine\entity\Location ? $args[0] : null, null);
    }

    public function canBeCollidedWith(mixed ...$args): bool { return false; }
    public function getDespawnDelay(mixed ...$args): int { return $this->despawnDelay; }
    public static function getMaxOrbSize(mixed ...$args): int
    {
        $amount = max(0, (int) ($args[0] ?? 0));
        foreach (self::ORB_SPLIT_SIZES as $size) {
            if ($amount >= $size) {
                return $size;
            }
        }
        return 1;
    }
    public static function getNetworkTypeId(mixed ...$args): string { return 'minecraft:xp_orb'; }
    public function getTargetPlayer(mixed ...$args): ?Entity { return $this->targetPlayer; }
    public function getXpValue(mixed ...$args): int { return $this->xpValue; }
    public function hasTargetPlayer(mixed ...$args): bool { return $this->targetPlayer !== null; }
    public function saveNBT(mixed ...$args): CompoundTag
    {
        $nbt = parent::saveNBT();
        $nbt->setShort(self::TAG_VALUE_PC, $this->xpValue);
        $nbt->setInt(self::TAG_VALUE_PE, $this->xpValue);
        $nbt->setShort('Age', $this->despawnDelay === self::NEVER_DESPAWN ? -32768 : max(-32768, self::DEFAULT_DESPAWN_DELAY - $this->despawnDelay));
        return $nbt;
    }
    public function setDespawnDelay(mixed ...$args): void
    {
        $delay = (int) ($args[0] ?? 0);
        if (($delay < 0 || $delay > self::MAX_DESPAWN_DELAY) && $delay !== self::NEVER_DESPAWN) {
            throw new \InvalidArgumentException('Despawn ticker must be in range 0 ... ' . self::MAX_DESPAWN_DELAY . ' or ' . self::NEVER_DESPAWN);
        }
        $this->despawnDelay = $delay;
    }
    public function setTargetPlayer(mixed ...$args): void { $this->targetPlayer = ($args[0] ?? null) instanceof Entity ? $args[0] : null; }
    public function setXpValue(mixed ...$args): void
    {
        $amount = (int) ($args[0] ?? 0);
        if ($amount <= 0) {
            throw new \InvalidArgumentException('XP amount must be greater than 0');
        }
        $this->xpValue = $amount;
    }
    public static function splitIntoOrbSizes(mixed ...$args): array
    {
        $amount = max(0, (int) ($args[0] ?? 0));
        $sizes = [];
        while ($amount > 0) {
            $size = self::getMaxOrbSize($amount);
            $sizes[] = $size;
            $amount -= $size;
        }
        return $sizes;
    }
}
