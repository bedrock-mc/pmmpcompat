<?php

declare(strict_types=1);

namespace pocketmine\world\biome;

class Biome
{
    public const MAX_BIOMES = 256;

    private int $id = 0;
    private bool $registered = false;
    /** @var object[] */
    private array $populators = [];
    private int $minElevation = 0;
    private int $maxElevation = 0;
    /** @var object[] */
    private array $groundCover = [];
    protected float $rainfall = 0.5;
    protected float $temperature = 0.5;

    public function addPopulator(object $populator): void { $this->populators[] = $populator; }
    public function clearPopulators(): void { $this->populators = []; }
    /** @return object[] */
    public function getGroundCover(): array { return $this->groundCover; }
    public function getId(): int { return $this->id; }
    public function getMaxElevation(): int { return $this->maxElevation; }
    public function getMinElevation(): int { return $this->minElevation; }
    public function getName(): string { return 'Unknown'; }
    /** @return object[] */
    public function getPopulators(): array { return $this->populators; }
    public function getRainfall(): float { return $this->rainfall; }
    public function getTemperature(): float { return $this->temperature; }
    public function populateChunk(mixed $world, int $chunkX, int $chunkZ, mixed $random): void
    {
        foreach ($this->populators as $populator) {
            if (method_exists($populator, 'populate')) {
                $populator->populate($world, $chunkX, $chunkZ, $random);
            }
        }
    }
    public function setElevation(int $min, int $max): void { $this->minElevation = $min; $this->maxElevation = $max; }
    /** @param object[] $covers */
    public function setGroundCover(array $covers): void { $this->groundCover = $covers; }
    public function setId(int $id): void
    {
        if (!$this->registered) {
            $this->registered = true;
            $this->id = $id;
        }
    }
}
