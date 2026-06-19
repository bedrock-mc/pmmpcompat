<?php

declare(strict_types=1);

namespace pocketmine\world\generator\noise;

class Noise
{
    protected int $seed;

    public function __construct(mixed $seed = 0, protected int $octaves = 1, protected float $persistence = 0.5, protected float $expansion = 1.0)
    {
        $this->seed = is_object($seed) && method_exists($seed, 'getSeed') ? (int) $seed->getSeed() : (is_scalar($seed) ? (int) $seed : 0);
        $this->octaves = max(1, $this->octaves);
        $this->expansion = $this->expansion === 0.0 ? 1.0 : $this->expansion;
    }

    public static function linearLerp(float $x, float $x1, float $x2): float
    {
        return $x1 + $x * ($x2 - $x1);
    }

    public static function bilinearLerp(float $x, float $y, float $q00, float $q01, float $q10, float $q11): float
    {
        $x0 = self::linearLerp($x, $q00, $q10);
        $x1 = self::linearLerp($x, $q01, $q11);
        return self::linearLerp($y, $x0, $x1);
    }

    public static function trilinearLerp(float $x, float $y, float $z, float $q000, float $q001, float $q010, float $q011, float $q100, float $q101, float $q110, float $q111): float
    {
        $z0 = self::bilinearLerp($x, $y, $q000, $q010, $q100, $q110);
        $z1 = self::bilinearLerp($x, $y, $q001, $q011, $q101, $q111);
        return self::linearLerp($z, $z0, $z1);
    }

    public function getFastNoise1D(float $x): float
    {
        return $this->getNoise2D($x, 0.0);
    }

    public function getFastNoise2D(float $x, float $y): float
    {
        return $this->getNoise2D($x, $y);
    }

    public function getFastNoise3D(float $x, float $y, float $z): float
    {
        return $this->getNoise3D($x, $y, $z);
    }

    public function getNoise2D(float $x, float $y): float
    {
        $result = 0.0;
        $amplitude = 1.0;
        $frequency = 1.0 / $this->expansion;
        $max = 0.0;
        for ($i = 0; $i < $this->octaves; ++$i) {
            $result += $this->noise2D($x * $frequency, $y * $frequency) * $amplitude;
            $max += $amplitude;
            $amplitude *= $this->persistence;
            $frequency *= 2.0;
        }
        return $max === 0.0 ? 0.0 : $result / $max;
    }

    public function getNoise3D(float $x, float $y, float $z): float
    {
        $result = 0.0;
        $amplitude = 1.0;
        $frequency = 1.0 / $this->expansion;
        $max = 0.0;
        for ($i = 0; $i < $this->octaves; ++$i) {
            $result += $this->noise3D($x * $frequency, $y * $frequency, $z * $frequency) * $amplitude;
            $max += $amplitude;
            $amplitude *= $this->persistence;
            $frequency *= 2.0;
        }
        return $max === 0.0 ? 0.0 : $result / $max;
    }

    public function noise2D(float $x, float $y): float
    {
        return $this->valueNoise($x, $y, 0.0);
    }

    public function noise3D(float $x, float $y, float $z): float
    {
        return $this->valueNoise($x, $y, $z);
    }

    protected function valueNoise(float $x, float $y, float $z): float
    {
        $x0 = (int) floor($x);
        $y0 = (int) floor($y);
        $z0 = (int) floor($z);
        $xf = $this->fade($x - $x0);
        $yf = $this->fade($y - $y0);
        $zf = $this->fade($z - $z0);

        return self::trilinearLerp(
            $xf,
            $yf,
            $zf,
            $this->hashValue($x0, $y0, $z0),
            $this->hashValue($x0, $y0, $z0 + 1),
            $this->hashValue($x0, $y0 + 1, $z0),
            $this->hashValue($x0, $y0 + 1, $z0 + 1),
            $this->hashValue($x0 + 1, $y0, $z0),
            $this->hashValue($x0 + 1, $y0, $z0 + 1),
            $this->hashValue($x0 + 1, $y0 + 1, $z0),
            $this->hashValue($x0 + 1, $y0 + 1, $z0 + 1),
        );
    }

    protected function fade(float $value): float
    {
        return $value * $value * $value * ($value * ($value * 6.0 - 15.0) + 10.0);
    }

    protected function hashValue(int $x, int $y, int $z): float
    {
        $hash = crc32($this->seed . ':' . $x . ':' . $y . ':' . $z);
        return ($hash / 0x7fffffff) - 1.0;
    }
}
