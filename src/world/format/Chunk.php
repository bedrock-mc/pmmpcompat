<?php

declare(strict_types=1);

namespace pocketmine\world\format;

use pocketmine\block\Block;

class Chunk
{
    public const DIRTY_FLAG_BLOCKS = 1 << 0;
    public const DIRTY_FLAG_BIOMES = 1 << 3;
    public const DIRTY_FLAGS_ALL = ~0;
    public const DIRTY_FLAGS_NONE = 0;
    public const MIN_SUBCHUNK_INDEX = -4;
    public const MAX_SUBCHUNK_INDEX = 19;
    public const MAX_SUBCHUNKS = self::MAX_SUBCHUNK_INDEX - self::MIN_SUBCHUNK_INDEX + 1;
    public const EDGE_LENGTH = SubChunk::EDGE_LENGTH;
    public const COORD_BIT_SIZE = SubChunk::COORD_BIT_SIZE;
    public const COORD_MASK = SubChunk::COORD_MASK;

    private int $terrainDirtyFlags = self::DIRTY_FLAGS_ALL;
    protected ?bool $lightPopulated = false;
    protected bool $terrainPopulated = false;
    /** @var \SplFixedArray<SubChunk> */
    protected \SplFixedArray $subChunks;
    /** @var array<int, object> */
    protected array $tiles = [];
    protected HeightArray $heightMap;

    /**
     * @param array<int, SubChunk> $subChunks
     */
    public function __construct(array $subChunks = [], bool $terrainPopulated = false)
    {
        $this->subChunks = new \SplFixedArray(self::MAX_SUBCHUNKS);
        for ($offset = 0; $offset < self::MAX_SUBCHUNKS; ++$offset) {
            $y = $offset + self::MIN_SUBCHUNK_INDEX;
            $this->subChunks[$offset] = $subChunks[$y] ?? new SubChunk(Block::EMPTY_STATE_ID, [], new PalettedBlockArray(0));
        }
        $this->heightMap = HeightArray::fill((self::MAX_SUBCHUNK_INDEX + 1) * SubChunk::EDGE_LENGTH);
        $this->terrainPopulated = $terrainPopulated;
    }

    public function getHeight(): int
    {
        return $this->subChunks->getSize();
    }

    public function getBlockStateId(int $x, int $y, int $z): int
    {
        return $this->getSubChunk($y >> SubChunk::COORD_BIT_SIZE)->getBlockStateId($x, $y & SubChunk::COORD_MASK, $z);
    }

    public function setBlockStateId(int $x, int $y, int $z, int $block): void
    {
        $this->getSubChunk($y >> SubChunk::COORD_BIT_SIZE)->setBlockStateId($x, $y & SubChunk::COORD_MASK, $z, $block);
        $this->terrainDirtyFlags |= self::DIRTY_FLAG_BLOCKS;
    }

    public function getHighestBlockAt(int $x, int $z): ?int
    {
        for ($y = self::MAX_SUBCHUNK_INDEX; $y >= self::MIN_SUBCHUNK_INDEX; --$y) {
            $height = $this->getSubChunk($y)->getHighestBlockAt($x, $z);
            if ($height !== null) {
                return $height | ($y << SubChunk::COORD_BIT_SIZE);
            }
        }
        return null;
    }

    public function getHeightMap(int $x, int $z): int
    {
        return $this->heightMap->get($x, $z);
    }

    public function setHeightMap(int $x, int $z, int $value): void
    {
        $this->heightMap->set($x, $z, $value);
    }

    public function getBiomeId(int $x, int $y, int $z): int
    {
        return $this->getSubChunk($y >> SubChunk::COORD_BIT_SIZE)->getBiomeArray()->get($x, $y & SubChunk::COORD_MASK, $z);
    }

    public function setBiomeId(int $x, int $y, int $z, int $biomeId): void
    {
        $this->getSubChunk($y >> SubChunk::COORD_BIT_SIZE)->getBiomeArray()->set($x, $y & SubChunk::COORD_MASK, $z, $biomeId);
        $this->terrainDirtyFlags |= self::DIRTY_FLAG_BIOMES;
    }

    public function isLightPopulated(): ?bool
    {
        return $this->lightPopulated;
    }

    public function setLightPopulated(?bool $value = true): void
    {
        $this->lightPopulated = $value;
    }

    public function isPopulated(): bool
    {
        return $this->terrainPopulated;
    }

    public function setPopulated(bool $value = true): void
    {
        $this->terrainPopulated = $value;
        $this->terrainDirtyFlags |= self::DIRTY_FLAG_BLOCKS;
    }

    public function addTile(object $tile): void
    {
        if (method_exists($tile, 'isClosed') && $tile->isClosed()) {
            throw new \InvalidArgumentException('Attempted to add a garbage closed Tile to a chunk');
        }
        if (!method_exists($tile, 'getPosition')) {
            throw new \InvalidArgumentException('Tile object must expose getPosition()');
        }
        $pos = $tile->getPosition();
        $index = self::blockHash((int) $pos->x, (int) $pos->y, (int) $pos->z);
        if (isset($this->tiles[$index]) && $this->tiles[$index] !== $tile) {
            throw new \InvalidArgumentException('Another tile is already at this location');
        }
        $this->tiles[$index] = $tile;
    }

    public function removeTile(object $tile): void
    {
        if (method_exists($tile, 'getPosition')) {
            $pos = $tile->getPosition();
            unset($this->tiles[self::blockHash((int) $pos->x, (int) $pos->y, (int) $pos->z)]);
        }
    }

    /**
     * @return array<int, object>
     */
    public function getTiles(): array
    {
        return $this->tiles;
    }

    public function getTile(int $x, int $y, int $z): ?object
    {
        return $this->tiles[self::blockHash($x, $y, $z)] ?? null;
    }

    public function onUnload(): void
    {
        foreach ($this->tiles as $tile) {
            if (method_exists($tile, 'close')) {
                $tile->close();
            }
        }
    }

    /**
     * @return int[]
     */
    public function getHeightMapArray(): array
    {
        return $this->heightMap->getValues();
    }

    /**
     * @param int[] $values
     */
    public function setHeightMapArray(array $values): void
    {
        $this->heightMap = new HeightArray($values);
    }

    public function isTerrainDirty(): bool
    {
        return $this->terrainDirtyFlags !== self::DIRTY_FLAGS_NONE;
    }

    public function getTerrainDirtyFlag(int $flag): bool
    {
        return ($this->terrainDirtyFlags & $flag) !== 0;
    }

    public function getTerrainDirtyFlags(): int
    {
        return $this->terrainDirtyFlags;
    }

    public function setTerrainDirtyFlag(int $flag, bool $value): void
    {
        if ($value) {
            $this->terrainDirtyFlags |= $flag;
        } else {
            $this->terrainDirtyFlags &= ~$flag;
        }
    }

    public function setTerrainDirty(): void
    {
        $this->terrainDirtyFlags = self::DIRTY_FLAGS_ALL;
    }

    public function clearTerrainDirtyFlags(): void
    {
        $this->terrainDirtyFlags = self::DIRTY_FLAGS_NONE;
    }

    public function getSubChunk(int $y): SubChunk
    {
        if ($y < self::MIN_SUBCHUNK_INDEX || $y > self::MAX_SUBCHUNK_INDEX) {
            throw new \InvalidArgumentException("Invalid subchunk Y coordinate $y");
        }
        return $this->subChunks[$y - self::MIN_SUBCHUNK_INDEX];
    }

    public function setSubChunk(int $y, ?SubChunk $subChunk): void
    {
        if ($y < self::MIN_SUBCHUNK_INDEX || $y > self::MAX_SUBCHUNK_INDEX) {
            throw new \InvalidArgumentException("Invalid subchunk Y coordinate $y");
        }
        $this->subChunks[$y - self::MIN_SUBCHUNK_INDEX] = $subChunk ?? new SubChunk(Block::EMPTY_STATE_ID, [], new PalettedBlockArray(0));
        $this->terrainDirtyFlags |= self::DIRTY_FLAG_BLOCKS;
    }

    /**
     * @return array<int, SubChunk>
     */
    public function getSubChunks(): array
    {
        $result = [];
        foreach ($this->subChunks as $offset => $subChunk) {
            $result[$offset + self::MIN_SUBCHUNK_INDEX] = $subChunk;
        }
        return $result;
    }

    public function collectGarbage(): void
    {
        foreach ($this->subChunks as $subChunk) {
            $subChunk->collectGarbage();
        }
    }

    public function __clone()
    {
        $this->subChunks = \SplFixedArray::fromArray(array_map(static fn(SubChunk $subChunk): SubChunk => clone $subChunk, $this->subChunks->toArray()));
        $this->heightMap = clone $this->heightMap;
    }

    public static function blockHash(int $x, int $y, int $z): int
    {
        return ($y << (2 * SubChunk::COORD_BIT_SIZE)) |
            (($z & SubChunk::COORD_MASK) << SubChunk::COORD_BIT_SIZE) |
            ($x & SubChunk::COORD_MASK);
    }
}
