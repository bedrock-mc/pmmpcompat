<?php

declare(strict_types=1);

namespace pocketmine\world;

class WorldManager
{
    public const TICKS_PER_AUTOSAVE = 6000;

    /** @var array<int, World> */
    private array $worlds = [];
    private ?World $defaultWorld = null;
    private bool $autoSave = true;
    private int $autoSaveInterval = self::TICKS_PER_AUTOSAVE;

    public function __construct(private mixed $server = null, private string $dataPath = '', private mixed $providerManager = null) {}

    public function getProviderManager(): mixed { return $this->providerManager; }
    /** @return array<int, World> */
    public function getWorlds(): array { return $this->worlds; }
    public function getDefaultWorld(): ?World { return $this->defaultWorld; }
    public function setDefaultWorld(?World $world): void { $this->defaultWorld = $world; if ($world !== null) { $this->worlds[$world->getId()] = $world; } }
    public function getWorld(int $worldId): ?World { return $this->worlds[$worldId] ?? null; }
    public function getWorldByName(string $name): ?World
    {
        foreach ($this->worlds as $world) {
            if (strcasecmp($world->getFolderName(), $name) === 0 || strcasecmp($world->getDisplayName(), $name) === 0) {
                return $world;
            }
        }
        return null;
    }
    public function isWorldLoaded(string $name): bool { return $this->getWorldByName($name) !== null; }
    public function isWorldGenerated(string $name): bool { return isset($this->worlds[$name]) || is_dir($this->dataPath . DIRECTORY_SEPARATOR . $name); }
    public function generateWorld(string $name, ?WorldCreationOptions $options = null): bool
    {
        if ($this->getWorldByName($name) !== null) {
            return false;
        }
        $world = new World($name);
        $this->worlds[$world->getId()] = $world;
        $this->defaultWorld ??= $world;
        return true;
    }
    public function loadWorld(string $name, bool $autoUpgrade = true): bool { return $this->generateWorld($name); }
    public function unloadWorld(World $world, bool $force = false): bool { unset($this->worlds[$world->getId()]); if ($this->defaultWorld === $world) { $this->defaultWorld = null; } return true; }
    public function findEntity(int $entityId): ?object
    {
        foreach ($this->worlds as $world) {
            if (($entity = $world->getEntity($entityId)) !== null) {
                return $entity;
            }
        }
        return null;
    }
    public function getAutoSave(): bool { return $this->autoSave; }
    public function setAutoSave(bool $autoSave): void { $this->autoSave = $autoSave; }
    public function getAutoSaveInterval(): int { return $this->autoSaveInterval; }
    public function setAutoSaveInterval(int $ticks): void { $this->autoSaveInterval = max(1, $ticks); }
    public function tick(int $currentTick): void {}
}
