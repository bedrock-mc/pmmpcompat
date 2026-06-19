<?php

declare(strict_types=1);

namespace pocketmine\world\biome;

class MountainsBiome extends GrassyBiome
{
    public function __construct()
    {
        parent::__construct();
        $this->setElevation(63, 127);
        $this->temperature = 0.4;
        $this->rainfall = 0.5;
    }

    public function getName(): string { return 'Mountains'; }
}
