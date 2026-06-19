<?php

declare(strict_types=1);

namespace pocketmine\world\generator\noise;

class Simplex extends Noise
{
    public function __construct(mixed $seed = 0, int $octaves = 1, float $persistence = 0.5, float $expansion = 1.0)
    {
        parent::__construct($seed, $octaves, $persistence, $expansion);
    }

    public function getNoise2D(float $x, float $y): float
    {
        return parent::getNoise2D($x, $y);
    }

    public function getNoise3D(float $x, float $y, float $z): float
    {
        return parent::getNoise3D($x, $y, $z);
    }

    public function noise2D(float $x, float $y): float
    {
        $skew = ($x + $y) * 0.3660254037844386;
        return $this->valueNoise($x + $skew, $y + $skew, 0.0);
    }

    public function noise3D(float $x, float $y, float $z): float
    {
        $skew = ($x + $y + $z) / 3.0;
        return $this->valueNoise($x + $skew, $y + $skew, $z + $skew);
    }
}
