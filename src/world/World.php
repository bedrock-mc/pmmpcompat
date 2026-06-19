<?php

declare(strict_types=1);

namespace pocketmine\world;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\MainLogger;

class World
{
    public const Y_MIN = -64;
    public const Y_MAX = 320;
    public const TIME_DAY = 0;
    public const TIME_NOON = 6000;
    public const TIME_SUNSET = 12000;
    public const TIME_NIGHT = 14000;
    public const TIME_MIDNIGHT = 18000;
    public const TIME_SUNRISE = 23000;
    public const TIME_FULL = 24000;
    public const DIFFICULTY_PEACEFUL = 0;
    public const DIFFICULTY_EASY = 1;
    public const DIFFICULTY_NORMAL = 2;
    public const DIFFICULTY_HARD = 3;
    public const DEFAULT_TICKED_BLOCKS_PER_SUBCHUNK_PER_TICK = 3;

    /** @var array<string, Block> */
    private array $blocks = [];
    /** @var array<string, Player> */
    private array $players = [];
    /** @var object[] */
    private array $entities = [];
    private MainLogger $logger;
    private int $time = self::TIME_DAY;
    private bool $timeStopped = false;
    private bool $autoSave = true;
    private int $difficulty = self::DIFFICULTY_NORMAL;
    private int $chunkTickRadius = 4;
    private Position $spawnLocation;
    private int $id;
    private static int $nextId = 1;

    public function __construct(private string $folderName)
    {
        $this->logger = new MainLogger('world:' . $folderName);
        $this->spawnLocation = new Position(0, 64, 0, $this);
        $this->id = self::$nextId++;
    }

    public function getFolderName(): string { return $this->folderName; }
    public function getDisplayName(): string { return $this->folderName; }
    public function setDisplayName(string $name): void { $this->folderName = $name; }
    public function __debugInfo(): array { return ['folderName' => $this->folderName, 'id' => $this->id, 'blocks' => count($this->blocks), 'players' => count($this->players)]; }
    public function getId(): int { return $this->id; }
    public function getServer(): Server { return Server::getInstance(); }
    public function getLogger(): MainLogger { return $this->logger; }
    public function getProvider(): mixed { return null; }
    public function getSeed(): int { return 0; }
    public function isLoaded(): bool { return true; }

    public function getBlock(Vector3 $pos): Block
    {
        return $this->blocks[$this->key($pos)] ?? VanillaBlocks::AIR();
    }

    public function getBlockAt(int $x, int $y, int $z): Block { return $this->getBlock(new Vector3($x, $y, $z)); }
    public function getBlockXYZ(int $x, int $y, int $z): Block { return $this->getBlockAt($x, $y, $z); }

    public function setBlock(Vector3 $pos, Block $block): void
    {
        $this->blocks[$this->key($pos)] = $block;
    }

    public function setBlockAt(int $x, int $y, int $z, Block $block): void { $this->setBlock(new Vector3($x, $y, $z), $block); }

    public function addPlayer(Player $player): void
    {
        $this->players[$player->getUniqueId()] = $player;
    }

    public function removePlayer(Player $player): void
    {
        unset($this->players[$player->getUniqueId()]);
    }

    /** @return Player[] */
    public function getPlayers(): array
    {
        return array_values($this->players);
    }

    public function addEntity(object $entity): void { $this->entities[spl_object_id($entity)] = $entity; }
    public function removeEntity(object $entity): void { unset($this->entities[spl_object_id($entity)]); }
    /** @return object[] */
    public function getEntities(): array { return array_values($this->entities); }
    public function getEntity(int $id): ?object { return $this->entities[$id] ?? null; }

    public function getSpawnLocation(): Position { return $this->spawnLocation; }
    public function setSpawnLocation(Position $pos): void { $this->spawnLocation = $pos; }
    public function getSafeSpawn(?Vector3 $spawn = null): Position { return Position::fromObject($spawn ?? $this->spawnLocation, $this); }
    public function requestSafeSpawn(?Vector3 $spawn = null): Position { return $this->getSafeSpawn($spawn); }
    public function isInWorld(int $x, int $y, int $z): bool { return $y >= self::Y_MIN && $y < self::Y_MAX; }
    public function getMinY(): int { return self::Y_MIN; }
    public function getMaxY(): int { return self::Y_MAX; }
    public static function blockHash(int $x, int $y, int $z): string { return $x . ':' . $y . ':' . $z; }
    public static function chunkHash(int $chunkX, int $chunkZ): string { return $chunkX . ':' . $chunkZ; }
    public static function chunkBlockHash(int $x, int $y, int $z): string { return self::blockHash($x, $y, $z); }
    public static function getXZ(string $hash): array { [$x, $z] = array_map('intval', explode(':', $hash) + [0, 0]); return [$x, $z]; }

    public function getTime(): int { return $this->time; }
    public function setTime(int $time): void { $this->time = (($time % self::TIME_FULL) + self::TIME_FULL) % self::TIME_FULL; }
    public function getTimeOfDay(): int { return $this->time; }
    public function startTime(): void { $this->timeStopped = false; }
    public function stopTime(): void { $this->timeStopped = true; }
    public function sendTime(): void {}
    public function computeSunAnglePercentage(): float { return $this->time / self::TIME_FULL; }
    public function getSunAnglePercentage(): float { return $this->computeSunAnglePercentage(); }
    public function getSunAngleDegrees(): float { return $this->getSunAnglePercentage() * 360.0; }
    public function getSunAngleRadians(): float { return deg2rad($this->getSunAngleDegrees()); }
    public function computeSkyLightReduction(): int { return 0; }
    public function getSkyLightReduction(): int { return 0; }

    public function getAutoSave(): bool { return $this->autoSave; }
    public function setAutoSave(bool $value): void { $this->autoSave = $value; }
    public function save(bool $force = false): void {}
    public function saveChunks(): void {}
    public function getDifficulty(): int { return $this->difficulty; }
    public function setDifficulty(int $difficulty): void { $this->difficulty = max(self::DIFFICULTY_PEACEFUL, min(self::DIFFICULTY_HARD, $difficulty)); }
    public static function getDifficultyFromString(string $difficulty): int
    {
        return match (strtolower($difficulty)) {
            'peaceful', '0' => self::DIFFICULTY_PEACEFUL,
            'easy', '1' => self::DIFFICULTY_EASY,
            'hard', '3' => self::DIFFICULTY_HARD,
            default => self::DIFFICULTY_NORMAL,
        };
    }

    public function getChunkTickRadius(): int { return $this->chunkTickRadius; }
    public function setChunkTickRadius(int $radius): void { $this->chunkTickRadius = max(0, $radius); }
    public function getTickRateTime(): float { return 0.0; }
    public function isDoingTick(): bool { return false; }
    public function doTick(int $currentTick): void { if (!$this->timeStopped) { $this->setTime($this->time + 1); } }
    public function clearCache(): void {}
    public function doChunkGarbageCollection(): void {}

    public function getBiome(Vector3 $pos): mixed { return null; }
    public function getBiomeId(int $x, int $y, int $z): int { return 0; }
    public function setBiomeId(int $x, int $y, int $z, int $biomeId): void {}
    public function getBlockLightAt(int $x, int $y, int $z): int { return 0; }
    public function getFullLightAt(int $x, int $y, int $z): int { return 15; }
    public function getFullLight(Vector3 $pos): int { return 15; }
    public function getPotentialLight(Vector3 $pos): int { return 15; }
    public function getPotentialLightAt(int $x, int $y, int $z): int { return 15; }
    public function getPotentialBlockSkyLightAt(int $x, int $y, int $z): int { return 15; }
    public function getRealBlockSkyLightAt(int $x, int $y, int $z): int { return 15; }
    public function getHighestAdjacentBlockLight(int $x, int $y, int $z): int { return 15; }
    public function getHighestAdjacentFullLightAt(int $x, int $y, int $z): int { return 15; }
    public function getHighestAdjacentPotentialLightAt(int $x, int $y, int $z): int { return 15; }
    public function getHighestAdjacentPotentialBlockSkyLight(int $x, int $y, int $z): int { return 15; }
    public function getHighestAdjacentRealBlockSkyLight(int $x, int $y, int $z): int { return 15; }
    public function getHighestBlockAt(int $x, int $z): int { return 0; }
    public function updateAllLight(Vector3 $pos): void {}

    public function isChunkLoaded(int $chunkX, int $chunkZ): bool { return true; }
    public function isChunkGenerated(int $chunkX, int $chunkZ): bool { return true; }
    public function isChunkPopulated(int $chunkX, int $chunkZ): bool { return true; }
    public function isChunkInUse(int $chunkX, int $chunkZ): bool { return false; }
    public function isChunkLocked(int $chunkX, int $chunkZ): bool { return false; }
    public function isSpawnChunk(int $chunkX, int $chunkZ): bool { return $chunkX === 0 && $chunkZ === 0; }
    public function getChunk(int $chunkX, int $chunkZ): mixed { return null; }
    public function setChunk(int $chunkX, int $chunkZ, mixed $chunk): void {}
    public function loadChunk(int $chunkX, int $chunkZ): bool { return true; }
    public function unloadChunk(int $chunkX, int $chunkZ, bool $safe = true): bool { return false; }
    public function unloadChunkRequest(int $chunkX, int $chunkZ, bool $safe = true): bool { return false; }
    public function cancelUnloadChunkRequest(int $chunkX, int $chunkZ): void {}
    public function unloadChunks(bool $force = false): void {}
    public function lockChunk(int $chunkX, int $chunkZ): void {}
    public function unlockChunk(int $chunkX, int $chunkZ): void {}
    public function orderChunkPopulation(int $chunkX, int $chunkZ, mixed $chunk): void {}
    public function requestChunkPopulation(int $chunkX, int $chunkZ, mixed $chunk): void {}
    public function getOrLoadChunkAtPosition(Vector3 $pos): mixed { return null; }
    public function getAdjacentChunks(int $chunkX, int $chunkZ): array { return []; }
    public function getLoadedChunks(): array { return []; }
    public function getChunkPlayers(int $chunkX, int $chunkZ): array { return []; }
    public function getChunkEntities(int $chunkX, int $chunkZ): array { return []; }
    public function getChunkLoaders(int $chunkX, int $chunkZ): array { return []; }
    public function getChunkListeners(int $chunkX, int $chunkZ): array { return []; }
    public function getTickingChunks(): array { return []; }
    public function registerChunkLoader(mixed ...$args): void {}
    public function unregisterChunkLoader(mixed ...$args): void {}
    public function registerChunkListener(mixed ...$args): void {}
    public function unregisterChunkListener(mixed ...$args): void {}
    public function unregisterChunkListenerFromAll(mixed ...$args): void {}
    public function registerTickingChunk(int $chunkX, int $chunkZ): void {}
    public function unregisterTickingChunk(int $chunkX, int $chunkZ): void {}

    public function addOnUnloadCallback(mixed $callback): void {}
    public function removeOnUnloadCallback(mixed $callback): void {}
    public function onUnload(): void {}
    public function addRandomTickedBlock(Block $block): void {}
    public function removeRandomTickedBlock(Block $block): void {}
    public function getRandomTickedBlocks(): array { return []; }
    public function addTile(mixed $tile): void {}
    public function removeTile(mixed $tile): void {}
    public function getTile(Vector3 $pos): mixed { return null; }
    public function getTileAt(int $x, int $y, int $z): mixed { return null; }
    public function addParticle(Vector3 $pos, mixed $particle, ?array $players = null): void {}
    public function addSound(Vector3 $pos, mixed $sound, ?array $players = null): void {}
    public function dropItem(Vector3 $pos, Item $item, ?Vector3 $motion = null, int $delay = 10): void {}
    public function dropExperience(Vector3 $pos, int $amount): void {}
    public function broadcastPacketToViewers(Vector3 $pos, mixed $packet): void {}
    public function getViewersForPosition(Vector3 $pos): array { return $this->getPlayers(); }
    public function createBlockUpdatePackets(array $blocks): array { return []; }
    public function notifyNeighbourBlockUpdate(Vector3 $pos): void {}
    public function scheduleDelayedBlockUpdate(Vector3 $pos, int $delay): void {}
    public function useBreakOn(Vector3 $pos, ?Item $item = null, ?Player $player = null, bool $createParticles = false): bool { return false; }
    public function useItemOn(Vector3 $pos, Item $item, int $face, Vector3 $clickVector, ?Player $player = null): bool { return false; }
    public function checkSleep(): void {}
    public function setSleepTicks(int $ticks): void {}
    public function onEntityMoved(object $entity): void {}
    public function getNearbyEntities(mixed ...$args): array { return []; }
    public function getNearestEntity(Vector3 $pos, float $maxDistance, ?string $entityType = null): ?object { return null; }
    public function getCollidingEntities(mixed ...$args): array { return []; }
    public function getCollisionBlocks(mixed ...$args): array { return []; }
    public function getCollisionBoxes(mixed ...$args): array { return []; }
    public function getBlockCollisionBoxes(mixed ...$args): array { return []; }
    public function isInLoadedTerrain(Vector3 $pos): bool { return true; }

    private function key(Vector3 $pos): string
    {
        return (int) floor($pos->x) . ':' . (int) floor($pos->y) . ':' . (int) floor($pos->z);
    }
}
