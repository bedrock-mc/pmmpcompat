<?php

declare(strict_types=1);

namespace pocketmine\world\format\io;

class BaseWorldProvider implements WorldProvider
{
    protected WorldData $worldData;
    /** @var array<string, LoadedChunkData> */
    protected array $chunks = [];

    public function __construct(protected string $path = '', ?WorldData $worldData = null)
    {
        $this->worldData = $worldData ?? new class(basename($path) ?: 'world') implements WorldData {
            private int $difficulty = 1;
            private int $time = 0;
            private \pocketmine\math\Vector3 $spawn;
            private int $rainTime = 0;
            private float $rainLevel = 0.0;
            private int $lightningTime = 0;
            private float $lightningLevel = 0.0;
            public function __construct(private string $name) { $this->spawn = new \pocketmine\math\Vector3(0, 64, 0); }
            public function getDifficulty(): int { return $this->difficulty; }
            public function getGenerator(): string { return 'default'; }
            public function getGeneratorOptions(): string { return ''; }
            public function getLightningLevel(): float { return $this->lightningLevel; }
            public function getLightningTime(): int { return $this->lightningTime; }
            public function getName(): string { return $this->name; }
            public function getRainLevel(): float { return $this->rainLevel; }
            public function getRainTime(): int { return $this->rainTime; }
            public function getSeed(): int { return 0; }
            public function getSpawn(): \pocketmine\math\Vector3 { return $this->spawn; }
            public function getTime(): int { return $this->time; }
            public function save(): void {}
            public function setDifficulty(int $difficulty): void { $this->difficulty = $difficulty; }
            public function setLightningLevel(float $level): void { $this->lightningLevel = $level; }
            public function setLightningTime(int $ticks): void { $this->lightningTime = $ticks; }
            public function setName(string $value): void { $this->name = $value; }
            public function setRainLevel(float $level): void { $this->rainLevel = $level; }
            public function setRainTime(int $ticks): void { $this->rainTime = $ticks; }
            public function setSpawn(\pocketmine\math\Vector3 $pos): void { $this->spawn = $pos; }
            public function setTime(int $value): void { $this->time = $value; }
        };
    }
    public function calculateChunkCount(): int { return count($this->chunks); }
    public function close(): void {}
    public function doGarbageCollection(): void {}
    public function getAllChunks(bool $skipCorrupted = false, ?\Logger $logger = null): \Generator
    {
        foreach ($this->chunks as $key => $chunk) {
            [$x, $z] = array_map('intval', explode(':', $key));
            yield [$x, $z] => $chunk;
        }
    }
    public function getPath(): string { return $this->path; }
    public function getWorldData(): WorldData { return $this->worldData; }
    public function getWorldMaxY(): int { return 320; }
    public function getWorldMinY(): int { return -64; }
    public function loadChunk(int $chunkX, int $chunkZ): ?LoadedChunkData { return $this->chunks[$chunkX . ':' . $chunkZ] ?? null; }
}
