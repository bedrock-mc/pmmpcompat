<?php

declare(strict_types=1);

namespace pocketmine\world\biome;

class ForestBiome extends GrassyBiome
{
    private string $type;

    public function __construct(mixed $type = 'Oak')
    {
        parent::__construct();
        $this->type = is_object($type) && method_exists($type, 'getDisplayName') ? (string) $type->getDisplayName() : (string) $type;
        $this->setElevation(63, 81);
        if (strcasecmp($this->type, 'Birch') === 0) {
            $this->temperature = 0.6;
            $this->rainfall = 0.5;
        } else {
            $this->temperature = 0.7;
            $this->rainfall = 0.8;
        }
    }

    public function getName(): string { return $this->type . ' Forest'; }
}
