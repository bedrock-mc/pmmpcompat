<?php

declare(strict_types=1);

namespace pocketmine\world\format;

class SubChunk
{
    public const COORD_BIT_SIZE = 4;
    public const COORD_MASK = ~(~0 << self::COORD_BIT_SIZE);
    public const EDGE_LENGTH = 1 << self::COORD_BIT_SIZE;

    /**
     * @param PalettedBlockArray[] $blockLayers
     */
    public function __construct(
        private int $emptyBlockId = 0,
        private array $blockLayers = [],
        private ?PalettedBlockArray $biomes = null,
        private ?LightArray $skyLight = null,
        private ?LightArray $blockLight = null,
    ) {
        $this->biomes ??= new PalettedBlockArray(0);
    }

    public function isEmptyAuthoritative(): bool
    {
        $this->collectGarbage();
        return $this->isEmptyFast();
    }

    public function isEmptyFast(): bool
    {
        return count($this->blockLayers) === 0;
    }

    public function getEmptyBlockId(): int
    {
        return $this->emptyBlockId;
    }

    public function getBlockStateId(int $x, int $y, int $z): int
    {
        return count($this->blockLayers) === 0 ? $this->emptyBlockId : $this->blockLayers[0]->get($x, $y, $z);
    }

    public function setBlockStateId(int $x, int $y, int $z, int $block): void
    {
        if (count($this->blockLayers) === 0) {
            $this->blockLayers[] = new PalettedBlockArray($this->emptyBlockId);
        }
        $this->blockLayers[0]->set($x, $y, $z, $block);
    }

    /**
     * @return PalettedBlockArray[]
     */
    public function getBlockLayers(): array
    {
        return $this->blockLayers;
    }

    public function getHighestBlockAt(int $x, int $z): ?int
    {
        if (count($this->blockLayers) === 0) {
            return null;
        }
        for ($y = self::EDGE_LENGTH - 1; $y >= 0; --$y) {
            if ($this->blockLayers[0]->get($x, $y, $z) !== $this->emptyBlockId) {
                return $y;
            }
        }
        return null;
    }

    public function getBiomeArray(): PalettedBlockArray
    {
        return $this->biomes;
    }

    public function getBlockSkyLightArray(): LightArray
    {
        return $this->skyLight ??= LightArray::fill(0);
    }

    public function setBlockSkyLightArray(LightArray $data): void
    {
        $this->skyLight = $data;
    }

    public function getBlockLightArray(): LightArray
    {
        return $this->blockLight ??= LightArray::fill(0);
    }

    public function setBlockLightArray(LightArray $data): void
    {
        $this->blockLight = $data;
    }

    public function collectGarbage(): void
    {
        $cleanedLayers = [];
        foreach ($this->blockLayers as $layer) {
            $layer->collectGarbage();
            if ($layer->getBitsPerBlock() !== 0 || $layer->get(0, 0, 0) !== $this->emptyBlockId) {
                $cleanedLayers[] = $layer;
            }
        }
        $this->blockLayers = $cleanedLayers;
        $this->biomes->collectGarbage();

        if ($this->skyLight !== null && $this->skyLight->isUniform(0)) {
            $this->skyLight = null;
        }
        if ($this->blockLight !== null && $this->blockLight->isUniform(0)) {
            $this->blockLight = null;
        }
    }

    public function __debugInfo(): array
    {
        return [];
    }

    public function __clone()
    {
        $this->blockLayers = array_map(static fn(PalettedBlockArray $array): PalettedBlockArray => clone $array, $this->blockLayers);
        $this->biomes = clone $this->biomes;
        $this->skyLight = $this->skyLight !== null ? clone $this->skyLight : null;
        $this->blockLight = $this->blockLight !== null ? clone $this->blockLight : null;
    }
}
