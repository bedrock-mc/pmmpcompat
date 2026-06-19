<?php

declare(strict_types=1);

namespace pocketmine\entity\effect;

use pocketmine\color\Color;

class Effect
{
    public function __construct(
        private string $name = 'Effect',
        private Color $color = new Color(0, 0, 0),
        private bool $bad = false,
        private int $defaultDuration = 600,
        private bool $bubbles = true,
    ) {}

    public function getName(): string { return $this->name; }
    public function getColor(): Color { return $this->color; }
    public function isBad(): bool { return $this->bad; }
    public function getDefaultDuration(): int { return $this->defaultDuration; }
    public function hasBubbles(): bool { return $this->bubbles; }
    public function canTick(EffectInstance $instance): bool { return false; }
    public function applyEffect(mixed ...$args): void {}
    public function add(mixed ...$args): void {}
    public function remove(mixed ...$args): void {}
}
