<?php

declare(strict_types=1);

namespace pocketmine\entity\object;

use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\nbt\tag\CompoundTag;

class AreaEffectCloud extends Entity
{
    public const DEFAULT_DURATION = 600;
    public const DEFAULT_DURATION_CHANGE_ON_USE = 0;
    public const DEFAULT_RADIUS = 3.0;
    public const DEFAULT_RADIUS_CHANGE_ON_PICKUP = 0.0;
    public const DEFAULT_RADIUS_CHANGE_ON_USE = -0.5;
    public const DEFAULT_RADIUS_CHANGE_PER_TICK = 0.0;
    public const REAPPLICATION_DELAY = 20;
    public const UPDATE_DELAY = 10;

    /** @var EffectInstance[] */
    private array $effects = [];
    private int $age = 0;
    private int $maxAge = self::DEFAULT_DURATION;
    private float $radius = self::DEFAULT_RADIUS;
    private int $maxAgeChangeOnUse = self::DEFAULT_DURATION_CHANGE_ON_USE;
    private float $radiusChangeOnPickup = self::DEFAULT_RADIUS_CHANGE_ON_PICKUP;
    private float $radiusChangeOnUse = self::DEFAULT_RADIUS_CHANGE_ON_USE;
    private float $radiusChangePerTick = self::DEFAULT_RADIUS_CHANGE_PER_TICK;
    private int $reapplicationDelay = self::REAPPLICATION_DELAY;

    public function __construct(mixed ...$args)
    {
        $location = null;
        $nbt = null;
        $effects = [];
        foreach ($args as $arg) {
            if ($arg instanceof Location && $location === null) {
                $location = $arg;
            } elseif ($arg instanceof CompoundTag && $nbt === null) {
                $nbt = $arg;
            } elseif ($arg instanceof EffectInstance) {
                $effects[] = $arg;
            }
        }
        $this->effects = $effects;
        parent::__construct($location, $nbt);
    }

    public static function getNetworkTypeId(mixed ...$args): string { return 'minecraft:area_effect_cloud'; }
    public function canBeCollidedWith(mixed ...$args): bool { return false; }
    public function getAge(): int { return $this->age; }
    /** @return EffectInstance[] */
    public function getEffects(): array { return $this->effects; }
    public function getInitialRadius(): float { return self::DEFAULT_RADIUS; }
    public function getMaxAge(): int { return $this->maxAge; }
    public function setMaxAge(int $maxAge): void { $this->maxAge = max(0, $maxAge); }
    public function getMaxAgeChangeOnUse(): int { return $this->maxAgeChangeOnUse; }
    public function setMaxAgeChangeOnUse(int $change): void { $this->maxAgeChangeOnUse = $change; }
    public function getRadius(): float { return $this->radius; }
    public function getRadiusChangeOnPickup(): float { return $this->radiusChangeOnPickup; }
    public function setRadiusChangeOnPickup(float $change): void { $this->radiusChangeOnPickup = $change; }
    public function getRadiusChangeOnUse(): float { return $this->radiusChangeOnUse; }
    public function setRadiusChangeOnUse(float $change): void { $this->radiusChangeOnUse = $change; }
    public function getRadiusChangePerTick(): float { return $this->radiusChangePerTick; }
    public function setRadiusChangePerTick(float $change): void { $this->radiusChangePerTick = $change; }
    public function getReapplicationDelay(): int { return $this->reapplicationDelay; }
    public function setReapplicationDelay(int $delay): void { $this->reapplicationDelay = max(0, $delay); }
    public function isFireProof(mixed ...$args): bool { return true; }
    public function setRadius(float $radius): void { $this->radius = max(0.0, $radius); }
    public function saveNBT(mixed ...$args): CompoundTag { return parent::saveNBT(); }
}
