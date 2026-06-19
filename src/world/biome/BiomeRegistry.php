<?php

declare(strict_types=1);

namespace pocketmine\world\biome;

use pocketmine\data\bedrock\BiomeIds;
use pocketmine\utils\SingletonTrait;

class BiomeRegistry
{
    use SingletonTrait;

    /** @var array<int, Biome> */
    private array $biomes = [];

    public function __construct()
    {
        $this->register(BiomeIds::OCEAN, new OceanBiome());
        $this->register(BiomeIds::PLAINS, new PlainBiome());
        $this->register(BiomeIds::DESERT, new DesertBiome());
        $this->register(BiomeIds::EXTREME_HILLS, new MountainsBiome());
        $this->register(BiomeIds::FOREST, new ForestBiome());
        $this->register(BiomeIds::TAIGA, new TaigaBiome());
        $this->register(BiomeIds::SWAMPLAND, new SwampBiome());
        $this->register(BiomeIds::RIVER, new RiverBiome());
        $this->register(BiomeIds::HELL, new HellBiome());
        $this->register(BiomeIds::ICE_PLAINS, new IcePlainsBiome());
        $this->register(BiomeIds::EXTREME_HILLS_EDGE, new SmallMountainsBiome());
        $this->register(BiomeIds::BIRCH_FOREST, new ForestBiome('Birch'));
    }

    public function getBiome(int $id): Biome
    {
        if (!isset($this->biomes[$id])) {
            $this->register($id, new UnknownBiome());
        }
        return $this->biomes[$id];
    }

    public function register(int $id, Biome $biome): void
    {
        $this->biomes[$id] = $biome;
        $biome->setId($id);
    }
}
