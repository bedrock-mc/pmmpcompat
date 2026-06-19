<?php

declare(strict_types=1);

namespace pocketmine\world\generator\biome;

use pocketmine\data\bedrock\BiomeIds;
use pocketmine\world\biome\Biome;
use pocketmine\world\biome\BiomeRegistry;
use pocketmine\world\generator\noise\Simplex;

class BiomeSelector
{
    /** @var array<int, Biome> */
    private array $lookup = [];
    private Simplex $temperature;
    private Simplex $rainfall;

    public function __construct(private mixed $random = null, private ?Biome $fallback = null)
    {
        $seed = is_object($random) && method_exists($random, 'getSeed') ? (int) $random->getSeed() : (is_scalar($random) ? (int) $random : 0);
        $this->temperature = new Simplex($seed ^ 0x13579, 4, 0.5, 256.0);
        $this->rainfall = new Simplex($seed ^ 0x24680, 4, 0.5, 256.0);
        $this->recalculate();
    }

    public function getTemperature(int $x, int $z): float
    {
        return $this->normalize($this->temperature->getNoise2D($x, $z));
    }

    public function getRainfall(int $x, int $z): float
    {
        return $this->normalize($this->rainfall->getNoise2D($x, $z));
    }

    public function pickBiome(int $x, int $z): Biome
    {
        $temperature = $this->getTemperature($x, $z);
        $rainfall = $this->getRainfall($x, $z);

        if ($temperature < 0.25) {
            return $this->lookup[BiomeIds::ICE_PLAINS];
        }
        if ($temperature > 0.85 && $rainfall < 0.35) {
            return $this->lookup[BiomeIds::DESERT];
        }
        if ($rainfall > 0.75) {
            return $this->lookup[BiomeIds::SWAMPLAND];
        }
        if ($rainfall > 0.55) {
            return $this->lookup[BiomeIds::FOREST];
        }
        return $this->lookup[BiomeIds::PLAINS] ?? $this->fallback ?? BiomeRegistry::getInstance()->getBiome(BiomeIds::PLAINS);
    }

    public function recalculate(): void
    {
        $registry = BiomeRegistry::getInstance();
        foreach ([BiomeIds::PLAINS, BiomeIds::DESERT, BiomeIds::FOREST, BiomeIds::SWAMPLAND, BiomeIds::ICE_PLAINS] as $id) {
            $this->lookup[$id] = $registry->getBiome($id);
        }
        if ($this->fallback !== null) {
            $this->lookup[BiomeIds::PLAINS] = $this->fallback;
        }
    }

    private function normalize(float $noise): float
    {
        return max(0.0, min(1.0, ($noise + 1.0) / 2.0));
    }
}
