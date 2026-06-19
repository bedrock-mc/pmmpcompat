<?php

declare(strict_types=1);

namespace pocketmine\world\light;

class SkyLightUpdate extends LightUpdate
{
    /** @param array<int, int> $lightFilters @param array<int, true> $directSkyLightBlockers */
    public function __construct(object|null $subChunkExplorer = null, array $lightFilters = [], private array $directSkyLightBlockers = [])
    {
        parent::__construct($subChunkExplorer, $lightFilters);
    }

    public function recalculateNode(int $x, int $y, int $z): void
    {
        $this->setAndUpdateLight($x, $y, $z, 15);
    }

    public function recalculateChunk(int $chunkX, int $chunkZ): int
    {
        return $this->execute();
    }
}
