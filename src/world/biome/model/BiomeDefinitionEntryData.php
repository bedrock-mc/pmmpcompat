<?php

declare(strict_types=1);

namespace pocketmine\world\biome\model;

class BiomeDefinitionEntryData
{
    /** @param string[] $tags */
    public function __construct(
        public int $id = 0,
        public float $temperature = 0.5,
        public float $downfall = 0.5,
        public float $foliageSnow = 0.0,
        public float $depth = 0.0,
        public float $scale = 0.0,
        public ?ColorData $mapWaterColour = null,
        public bool $rain = true,
        public array $tags = []
    ) {
        $this->mapWaterColour ??= new ColorData();
    }
}
