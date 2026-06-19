<?php

declare(strict_types=1);

namespace pocketmine\world\format\io;

class WorldProviderManager
{
    /** @var array<string, WorldProviderManagerEntry> */
    private array $providers = [];
    private WritableWorldProviderManagerEntry $default;

    public function __construct()
    {
        $this->default = new WritableWorldProviderManagerEntry(
            static fn(string $path): bool => is_dir($path),
            static fn(string $path, \Logger $logger): WritableWorldProvider => new class($path) extends BaseWorldProvider implements WritableWorldProvider {
                public function saveChunk(int $chunkX, int $chunkZ, ChunkData $chunkData, int $dirtyFlags): void {}
            },
            static function(string $path, string $name, mixed $options): void { @mkdir($path, 0777, true); }
        );
        $this->addProvider($this->default, 'leveldb');
    }
    public function addProvider(WorldProviderManagerEntry $providerEntry, string $name, bool $overwrite = false): void
    {
        $key = strtolower(trim($name));
        if (!$overwrite && isset($this->providers[$key])) {
            throw new \InvalidArgumentException('Alias "' . $key . '" is already assigned');
        }
        $this->providers[$key] = $providerEntry;
    }
    /** @return array<string, WorldProviderManagerEntry> */
    public function getAvailableProviders(): array { return $this->providers; }
    public function getDefault(): WritableWorldProviderManagerEntry { return $this->default; }
    /** @return array<string, WorldProviderManagerEntry> */
    public function getMatchingProviders(string $path): array
    {
        return array_filter($this->providers, static fn(WorldProviderManagerEntry $entry): bool => $entry->isValid($path));
    }
    public function getProviderByName(string $name): ?WorldProviderManagerEntry { return $this->providers[strtolower(trim($name))] ?? null; }
    public function setDefault(WritableWorldProviderManagerEntry $class): void { $this->default = $class; }
}
