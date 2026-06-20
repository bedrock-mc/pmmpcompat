<?php

declare(strict_types=1);

namespace pocketmine\world;

use pocketmine\math\Vector3;

class Position extends Vector3
{
    public function __construct(float $x, float $y, float $z, public ?World $world)
    {
        parent::__construct($x, $y, $z);
    }

    public static function fromObject(Vector3 $pos, ?World $world): self
    {
        return new self($pos->x, $pos->y, $pos->z, $world);
    }

    public function asPosition(): self
    {
        return new self($this->x, $this->y, $this->z, $this->world);
    }

    public function getWorld(): World
    {
        return $this->world ?? throw new \LogicException('Position world is not valid.');
    }

    public function isValid(): bool
    {
        return $this->world !== null;
    }

    public function getSide(int $side, int $step = 1): self
    {
        return self::fromObject(parent::getSide($side, $step), $this->world);
    }

    public function equals(Vector3 $v): bool
    {
        return parent::equals($v) && (!$v instanceof self || $v->world === $this->world);
    }

    public function __toString(): string
    {
        return 'Position(world=' . ($this->world?->getDisplayName() ?? 'null') . ',x=' . $this->x . ',y=' . $this->y . ',z=' . $this->z . ')';
    }
}
