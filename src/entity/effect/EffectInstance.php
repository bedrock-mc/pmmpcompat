<?php

declare(strict_types=1);

namespace pocketmine\entity\effect;

use pocketmine\color\Color;

class EffectInstance
{
    private mixed $type;
    private int $duration;
    private int $amplifier;
    private bool $visible;
    private bool $ambient;
    private ?Color $color = null;

    public function __construct(mixed $type = null, int $duration = 600, int $amplifier = 0, bool $visible = true, bool $ambient = false)
    {
        $this->type = $type;
        $this->duration = $duration;
        $this->amplifier = $amplifier;
        $this->visible = $visible;
        $this->ambient = $ambient;
    }

    public function getType(): mixed { return $this->type; }
    public function getDuration(): int { return $this->duration; }
    public function setDuration(int $duration): self { $this->duration = $duration; return $this; }
    public function decreaseDuration(int $ticks): self { $this->duration = max(0, $this->duration - $ticks); return $this; }
    public function hasExpired(): bool { return $this->duration <= 0; }
    public function getAmplifier(): int { return $this->amplifier; }
    public function setAmplifier(int $amplifier): self { $this->amplifier = $amplifier; return $this; }
    public function getEffectLevel(): int { return $this->amplifier + 1; }
    public function isVisible(): bool { return $this->visible; }
    public function setVisible(bool $visible): self { $this->visible = $visible; return $this; }
    public function isAmbient(): bool { return $this->ambient; }
    public function setAmbient(bool $ambient): self { $this->ambient = $ambient; return $this; }
    public function getColor(): ?Color { return $this->color; }
    public function setColor(Color $color): self { $this->color = $color; return $this; }
    public function resetColor(): self { $this->color = null; return $this; }
}
