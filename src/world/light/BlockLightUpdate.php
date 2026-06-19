<?php

declare(strict_types=1);

namespace pocketmine\world\light;

class BlockLightUpdate extends LightUpdate
{
    /** @param array<int, int> $lightFilters @param array<int, int> $lightEmitters */
    public function __construct(object|null $subChunkExplorer = null, array $lightFilters = [], private array $lightEmitters = [])
    {
        parent::__construct($subChunkExplorer, $lightFilters);
    }

    public function recalculateNode(int $x, int $y, int $z): void
    {
        $this->setAndUpdateLight($x, $y, $z, max(0, $this->lightEmitters[0] ?? 0));
    }

    public function recalculateChunk(int $chunkX, int $chunkZ): int
    {
        return $this->execute();
    }
}
