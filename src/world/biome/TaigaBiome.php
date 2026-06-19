<?php

declare(strict_types=1);

namespace pocketmine\world\biome;

class TaigaBiome extends SnowyBiome
{
    public function __construct()
    {
        parent::__construct();
        $this->setElevation(63, 81);
        $this->temperature = 0.05;
        $this->rainfall = 0.8;
    }

    public function getName(): string { return 'Taiga'; }
}
