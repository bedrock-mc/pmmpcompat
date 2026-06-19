<?php

declare(strict_types=1);

namespace pocketmine\world\format\io;

class LoadedChunkData
{
    public const FIXER_FLAG_ALL = ~0;
    public const FIXER_FLAG_NONE = 0;
    public function __construct(private ChunkData $data, private bool $upgraded = false, private int $fixerFlags = self::FIXER_FLAG_NONE) {}
    public function getData(): ChunkData { return $this->data; }
    public function getFixerFlags(): int { return $this->fixerFlags; }
    public function isUpgraded(): bool { return $this->upgraded; }
}
