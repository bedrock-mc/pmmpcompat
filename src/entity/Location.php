<?php

declare(strict_types=1);

namespace pocketmine\entity;

use pocketmine\math\Vector3;
use pocketmine\world\Position;
use pocketmine\world\World;

class Location extends Position
{
    public function __construct(float $x, float $y, float $z, ?World $world, public float $yaw, public float $pitch)
    {
        parent::__construct($x, $y, $z, $world);
    }

    public static function fromObject(Vector3 $pos, ?World $world, float $yaw = 0.0, float $pitch = 0.0): self
    {
        return new self($pos->x, $pos->y, $pos->z, $world ?? (($pos instanceof Position && $pos->isValid()) ? $pos->getWorld() : null), $yaw, $pitch);
    }
    public function asLocation(): self { return new self($this->x, $this->y, $this->z, $this->isValid() ? $this->getWorld() : null, $this->yaw, $this->pitch); }
    public function getYaw(): float { return $this->yaw; }
    public function getPitch(): float { return $this->pitch; }
    public function equals(Vector3 $v): bool { return parent::equals($v) && (!$v instanceof self || ($v->yaw === $this->yaw && $v->pitch === $this->pitch)); }
    public function __toString(): string { return 'Location(world=' . ($this->isValid() ? $this->getWorld()->getDisplayName() : 'null') . ',x=' . $this->x . ',y=' . $this->y . ',z=' . $this->z . ',yaw=' . $this->yaw . ',pitch=' . $this->pitch . ')'; }
}
