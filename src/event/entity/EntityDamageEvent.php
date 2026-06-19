<?php

declare(strict_types=1);

namespace pocketmine\event\entity;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

class EntityDamageEvent extends EntityEvent implements Cancellable
{
    use CancellableTrait;

    public const CAUSE_CONTACT = 0;
    public const CAUSE_ENTITY_ATTACK = 1;
    public const CAUSE_PROJECTILE = 2;
    public const CAUSE_SUFFOCATION = 3;
    public const CAUSE_FALL = 4;
    public const CAUSE_FIRE = 5;
    public const CAUSE_FIRE_TICK = 6;
    public const CAUSE_LAVA = 7;
    public const CAUSE_DROWNING = 8;
    public const CAUSE_BLOCK_EXPLOSION = 9;
    public const CAUSE_ENTITY_EXPLOSION = 10;
    public const CAUSE_VOID = 11;
    public const CAUSE_SUICIDE = 12;
    public const CAUSE_MAGIC = 13;
    public const CAUSE_CUSTOM = 14;
    public const CAUSE_STARVATION = 15;
    public const CAUSE_FALLING_BLOCK = 16;
    public const MODIFIER_ARMOR = 1;
    public const MODIFIER_STRENGTH = 2;
    public const MODIFIER_WEAKNESS = 3;
    public const MODIFIER_RESISTANCE = 4;
    public const MODIFIER_ABSORPTION = 5;
    public const MODIFIER_ARMOR_ENCHANTMENTS = 6;
    public const MODIFIER_CRITICAL = 7;
    public const MODIFIER_TOTEM = 8;
    public const MODIFIER_WEAPON_ENCHANTMENTS = 9;
    public const MODIFIER_PREVIOUS_DAMAGE_COOLDOWN = 10;
    public const MODIFIER_ARMOR_HELMET = 11;

    private float $originalBaseDamage;
    /** @var array<int, float> */
    private array $modifiers = [];
    /** @var array<int, float> */
    private array $originalModifiers = [];
    private int $attackCooldown = 10;

    /** @param array<int, float> $modifiers */
    public function __construct(object $entity, private float $baseDamage, private int $cause = self::CAUSE_CUSTOM, array $modifiers = [])
    {
        parent::__construct($entity);
        $this->originalBaseDamage = $baseDamage;
        $this->modifiers = $modifiers;
        $this->originalModifiers = $modifiers;
    }

    public function getCause(): int { return $this->cause; }
    public function getBaseDamage(): float { return $this->baseDamage; }
    public function setBaseDamage(float $damage): void { $this->baseDamage = $damage; }
    public function getOriginalBaseDamage(): float { return $this->originalBaseDamage; }
    /** @return array<int, float> */
    public function getModifiers(): array { return $this->modifiers; }
    public function getModifier(int $type): float { return $this->modifiers[$type] ?? 0.0; }
    public function setModifier(float $damage, int $type): void { $this->modifiers[$type] = $damage; }
    /** @return array<int, float> */
    public function getOriginalModifiers(): array { return $this->originalModifiers; }
    public function getOriginalModifier(int $type): float { return $this->originalModifiers[$type] ?? 0.0; }
    public function isApplicable(int $type): bool { return isset($this->modifiers[$type]); }
    public function getFinalDamage(): float { return max(0.0, $this->baseDamage + array_sum($this->modifiers)); }
    public function canBeReducedByArmor(): bool
    {
        return !in_array($this->cause, [self::CAUSE_FIRE_TICK, self::CAUSE_SUFFOCATION, self::CAUSE_DROWNING, self::CAUSE_STARVATION, self::CAUSE_FALL, self::CAUSE_VOID, self::CAUSE_MAGIC, self::CAUSE_SUICIDE], true);
    }
    public function getAttackCooldown(): int { return $this->attackCooldown; }
    public function setAttackCooldown(int $attackCooldown): void { $this->attackCooldown = $attackCooldown; }
}
