<?php

declare(strict_types=1);

namespace pocketmine\entity;

class Attribute
{
    public const MC_PREFIX = 'minecraft:';
    public const ABSORPTION = self::MC_PREFIX . 'absorption';
    public const SATURATION = self::MC_PREFIX . 'player.saturation';
    public const EXHAUSTION = self::MC_PREFIX . 'player.exhaustion';
    public const KNOCKBACK_RESISTANCE = self::MC_PREFIX . 'knockback_resistance';
    public const HEALTH = self::MC_PREFIX . 'health';
    public const MOVEMENT_SPEED = self::MC_PREFIX . 'movement';
    public const FOLLOW_RANGE = self::MC_PREFIX . 'follow_range';
    public const HUNGER = self::MC_PREFIX . 'player.hunger';
    public const FOOD = self::HUNGER;
    public const ATTACK_DAMAGE = self::MC_PREFIX . 'attack_damage';
    public const EXPERIENCE_LEVEL = self::MC_PREFIX . 'player.level';
    public const EXPERIENCE = self::MC_PREFIX . 'player.experience';
    public const UNDERWATER_MOVEMENT = self::MC_PREFIX . 'underwater_movement';
    public const LUCK = self::MC_PREFIX . 'luck';
    public const FALL_DAMAGE = self::MC_PREFIX . 'fall_damage';
    public const HORSE_JUMP_STRENGTH = self::MC_PREFIX . 'horse.jump_strength';
    public const ZOMBIE_SPAWN_REINFORCEMENTS = self::MC_PREFIX . 'zombie.spawn_reinforcements';
    public const LAVA_MOVEMENT = self::MC_PREFIX . 'lava_movement';

    private float $currentValue;
    private bool $desynchronized = true;

    public function __construct(
        private string $id,
        private float $minValue,
        private float $maxValue,
        private float $defaultValue,
        private bool $shouldSend = true,
    ) {
        if ($minValue > $maxValue || $defaultValue < $minValue || $defaultValue > $maxValue) {
            throw new \InvalidArgumentException('Invalid attribute range');
        }
        $this->currentValue = $defaultValue;
    }

    public function getId(): string { return $this->id; }
    public function getMinValue(): float { return $this->minValue; }
    public function getMaxValue(): float { return $this->maxValue; }
    public function getDefaultValue(): float { return $this->defaultValue; }
    public function getValue(): float { return $this->currentValue; }
    public function isSyncable(): bool { return $this->shouldSend; }
    public function isDesynchronized(): bool { return $this->shouldSend && $this->desynchronized; }
    public function markSynchronized(bool $synced = true): void { $this->desynchronized = !$synced; }
    public function setMinValue(float $minValue): self
    {
        if ($minValue > $this->maxValue) {
            throw new \InvalidArgumentException('Minimum is greater than maximum');
        }
        $this->minValue = $minValue;
        $this->desynchronized = true;
        return $this;
    }
    public function setMaxValue(float $maxValue): self
    {
        if ($maxValue < $this->minValue) {
            throw new \InvalidArgumentException('Maximum is less than minimum');
        }
        $this->maxValue = $maxValue;
        $this->desynchronized = true;
        return $this;
    }
    public function setDefaultValue(float $defaultValue): self
    {
        if ($defaultValue < $this->minValue || $defaultValue > $this->maxValue) {
            throw new \InvalidArgumentException('Default outside attribute range');
        }
        $this->defaultValue = $defaultValue;
        $this->desynchronized = true;
        return $this;
    }
    public function setValue(float $value, bool $fit = false, bool $forceSend = false): self
    {
        if ($value < $this->minValue || $value > $this->maxValue) {
            if (!$fit) {
                throw new \InvalidArgumentException('Value outside attribute range');
            }
            $value = min(max($value, $this->minValue), $this->maxValue);
        }
        if ($this->currentValue !== $value || $forceSend) {
            $this->desynchronized = true;
        }
        $this->currentValue = $value;
        return $this;
    }
    public function resetToDefault(): void { $this->setValue($this->defaultValue, true); }
}
