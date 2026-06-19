<?php

declare(strict_types=1);

namespace pocketmine\math;

class Vector3
{
    public function __construct(public float $x = 0, public float $y = 0, public float $z = 0) {}

    public static function zero(): self { return new self(); }
    public function getX(): float { return $this->x; }
    public function getY(): float { return $this->y; }
    public function getZ(): float { return $this->z; }
    public function getFloorX(): int { return (int) floor($this->x); }
    public function getFloorY(): int { return (int) floor($this->y); }
    public function getFloorZ(): int { return (int) floor($this->z); }
    public function add(float $x, float $y = 0.0, float $z = 0.0): self { return new self($this->x + $x, $this->y + $y, $this->z + $z); }
    public function distanceSquared(self $v): float { return (($this->x - $v->x) ** 2) + (($this->y - $v->y) ** 2) + (($this->z - $v->z) ** 2); }
    public function equals(self $v): bool { return $this->x === $v->x && $this->y === $v->y && $this->z === $v->z; }
    public function getSide(int $side, int $step = 1): self
    {
        return match ($side) {
            0 => new self($this->x, $this->y - $step, $this->z),
            1 => new self($this->x, $this->y + $step, $this->z),
            2 => new self($this->x, $this->y, $this->z - $step),
            3 => new self($this->x, $this->y, $this->z + $step),
            4 => new self($this->x - $step, $this->y, $this->z),
            5 => new self($this->x + $step, $this->y, $this->z),
            default => new self($this->x, $this->y, $this->z),
        };
    }
}
