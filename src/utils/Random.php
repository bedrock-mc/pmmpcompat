<?php

declare(strict_types=1);

namespace pocketmine\utils;

class Random
{
    public const X = 123456789;
    public const Y = 362436069;
    public const Z = 521288629;
    public const W = 88675123;

    private int $x;
    private int $y;
    private int $z;
    private int $w;
    protected int $seed;

    public function __construct(int $seed = -1)
    {
        $this->setSeed($seed === -1 ? time() : $seed);
    }

    public function setSeed(int $seed): void
    {
        $this->seed = $seed;
        $this->x = (self::X ^ $seed) & 0xffffffff;
        $this->y = (self::Y ^ (($seed << 17) | (($seed >> 15) & 0x7fffffff))) & 0xffffffff;
        $this->z = (self::Z ^ (($seed << 31) | (($seed >> 1) & 0x7fffffff))) & 0xffffffff;
        $this->w = (self::W ^ (($seed << 18) | (($seed >> 14) & 0x7fffffff))) & 0xffffffff;
    }

    public function getSeed(): int { return $this->seed; }
    public function nextInt(): int { return $this->nextSignedInt() & 0x7fffffff; }

    public function nextSignedInt(): int
    {
        $t = ($this->x ^ ($this->x << 11)) & 0xffffffff;
        $this->x = $this->y;
        $this->y = $this->z;
        $this->z = $this->w;
        $this->w = ($this->w ^ (($this->w >> 19) & 0x7fffffff) ^ ($t ^ (($t >> 8) & 0x7fffffff))) & 0xffffffff;
        return $this->w >= 0x80000000 ? $this->w - 0x100000000 : $this->w;
    }

    public function nextFloat(): float { return $this->nextInt() / 0x7fffffff; }
    public function nextSignedFloat(): float { return $this->nextSignedInt() / 0x7fffffff; }
    public function nextBoolean(): bool { return ($this->nextSignedInt() & 0x01) === 0; }
    public function nextRange(int $start = 0, int $end = 0x7fffffff): int { return $start + ($this->nextInt() % ($end + 1 - $start)); }
    public function nextBoundedInt(int $bound): int { return $this->nextInt() % $bound; }
}
