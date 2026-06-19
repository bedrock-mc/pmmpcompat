<?php

declare(strict_types=1);

namespace pocketmine\entity;

class Living extends Entity
{
    public const DEFAULT_KNOCKBACK_FORCE = 0.4;
    public const DEFAULT_KNOCKBACK_VERTICAL_LIMIT = 0.4;

    private float $health = 20.0;
    private float $maxHealth = 20.0;
    private float $absorption = 0.0;
    private int $airSupplyTicks = 300;
    private int $maxAirSupplyTicks = 300;
    private float $movementSpeed = 0.1;

    public function applyDamageModifiers(mixed ...$args): mixed { return null; }
    public function attack(mixed ...$args): mixed { return null; }
    public function canBeRenamed(mixed ...$args): bool { return true; }
    public function canBreathe(mixed ...$args): bool { return true; }
    public function consumeObject(mixed ...$args): mixed { return null; }
    public function damageArmor(mixed ...$args): mixed { return null; }
    public function getAbsorption(): float { return $this->absorption; }
    public function setAbsorption(float $absorption): void { $this->absorption = max(0.0, $absorption); }
    public function getAirSupplyTicks(): int { return $this->airSupplyTicks; }
    public function setAirSupplyTicks(int $ticks): void { $this->airSupplyTicks = max(0, $ticks); }
    public function getMaxAirSupplyTicks(): int { return $this->maxAirSupplyTicks; }
    public function setMaxAirSupplyTicks(int $ticks): void { $this->maxAirSupplyTicks = max(0, $ticks); }
    public function getArmorInventory(mixed ...$args): mixed { return null; }
    public function getArmorPoints(mixed ...$args): int { return 0; }
    public function getDisplayName(): string { return $this->getName(); }
    public function getDrops(mixed ...$args): array { return []; }
    public function getEffects(mixed ...$args): array { return []; }
    public function getFrostWalkerLevel(mixed ...$args): int { return 0; }
    public function getHighestArmorEnchantmentLevel(mixed ...$args): int { return 0; }
    public function getJumpVelocity(mixed ...$args): float { return 0.42; }
    public function getLineOfSight(mixed ...$args): array { return []; }
    public function getMaxHealth(mixed ...$args): mixed { return $this->maxHealth; }
    public function setMaxHealth(mixed ...$args): mixed { $this->maxHealth = max(1.0, (float) ($args[0] ?? $this->maxHealth)); return null; }
    public function getMovementSpeed(): float { return $this->movementSpeed; }
    public function setMovementSpeed(float $speed): void { $this->movementSpeed = max(0.0, $speed); }
    public function getName(mixed ...$args): string { return static::class; }
    public function getSneakOffset(mixed ...$args): float { return 0.0; }
    public function getTargetBlock(mixed ...$args): mixed { return null; }
    public function getXpDropAmount(mixed ...$args): int { return 0; }
    public function isBreathing(): bool { return $this->airSupplyTicks > 0; }
    public function setBreathing(mixed ...$args): mixed { return null; }
    public function isGliding(mixed ...$args): bool { return false; }
    public function setGliding(mixed ...$args): mixed { return null; }
    public function isSneaking(mixed ...$args): bool { return false; }
    public function setSneaking(mixed ...$args): mixed { return null; }
    public function isSprinting(mixed ...$args): bool { return false; }
    public function setSprinting(mixed ...$args): mixed { return null; }
    public function isSwimming(mixed ...$args): bool { return false; }
    public function setSwimming(mixed ...$args): mixed { return null; }
    public function jump(mixed ...$args): mixed { return null; }
    public function knockBack(mixed ...$args): mixed { return null; }
    public function lookAt(mixed ...$args): mixed { return null; }
    public function onAirExpired(mixed ...$args): mixed { return null; }
    public function saveNBT(mixed ...$args): mixed { return null; }
    public function setHealth(mixed ...$args): mixed { $this->health = max(0.0, min((float) ($args[0] ?? $this->health), $this->maxHealth)); return null; }
    public function getHealth(mixed ...$args): mixed { return $this->health; }
    public function setOnFire(mixed ...$args): mixed { return null; }
}
