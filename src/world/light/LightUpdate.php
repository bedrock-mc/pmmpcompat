<?php

declare(strict_types=1);

namespace pocketmine\world\light;

abstract class LightUpdate
{
    public const BASE_LIGHT_FILTER = 1;

    /** @var array<int|string, array{int, int, int, int}> */
    protected array $updateNodes = [];

    /**
     * @param object|null $subChunkExplorer Optional PMMP-style explorer. Kept local; no Dragonfly state is mutated.
     * @param array<int, int> $lightFilters
     */
    public function __construct(protected ?object $subChunkExplorer = null, protected array $lightFilters = [])
    {
    }

    abstract public function recalculateNode(int $x, int $y, int $z): void;

    abstract public function recalculateChunk(int $chunkX, int $chunkZ): int;

    public function setAndUpdateLight(int $x, int $y, int $z, int $newLevel): void
    {
        $this->updateNodes[self::blockHash($x, $y, $z)] = [$x, $y, $z, max(0, min(15, $newLevel))];
    }

    public function execute(): int
    {
        $touched = count($this->updateNodes);
        $this->updateNodes = [];
        return $touched;
    }

    protected static function blockHash(int $x, int $y, int $z): int
    {
        return (($x & 0x3fffff) << 42) ^ (($y & 0xfffff) << 22) ^ ($z & 0x3fffff);
    }
}
