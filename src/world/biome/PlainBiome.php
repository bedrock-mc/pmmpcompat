<?php

declare(strict_types=1);

namespace pocketmine\world\biome;

class PlainBiome extends GrassyBiome
{
    public function __construct()
    {
        parent::__construct();
        $this->setElevation(63, 68);
        $this->temperature = 0.8;
        $this->rainfall = 0.4;
    }

    public function getName(): string { return 'Plains'; }
}
