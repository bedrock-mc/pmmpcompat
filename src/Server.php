<?php

declare(strict_types=1);

namespace pocketmine;

use pocketmine\command\SimpleCommandMap;
use pocketmine\command\CommandSender;
use pocketmine\compat\ServerBridge;
use pocketmine\event\server\CommandEvent;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\permission\PermissionManager;
use pocketmine\player\Player;
use pocketmine\plugin\PluginManager;
use pocketmine\scheduler\AsyncPool;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\utils\MainLogger;
use pocketmine\world\World;
use pocketmine\world\WorldManager;

class Server
{
    public const BROADCAST_CHANNEL_ADMINISTRATIVE = 'pocketmine.broadcast.admin';
    public const BROADCAST_CHANNEL_USERS = 'pocketmine.broadcast.user';
    public const DEFAULT_SERVER_NAME = 'PocketMine-MP Server';
    public const DEFAULT_MAX_PLAYERS = 20;
    public const DEFAULT_PORT_IPV4 = 19132;
    public const DEFAULT_PORT_IPV6 = 19133;
    public const DEFAULT_MAX_VIEW_DISTANCE = 16;
    public const TARGET_TICKS_PER_SECOND = 20;
    public const TARGET_SECONDS_PER_TICK = 1 / self::TARGET_TICKS_PER_SECOND;
    public const TARGET_NANOSECONDS_PER_TICK = 1_000_000_000 / self::TARGET_TICKS_PER_SECOND;

    private static ?self $instance = null;
    private PluginManager $pluginManager;
    private SimpleCommandMap $commandMap;
    private TaskScheduler $scheduler;
    private AsyncPool $asyncPool;
    private TimeTrackingSleeperHandler $tickSleeper;
    private WorldManager $worldManager;
    private MainLogger $logger;
    private PermissionManager $permissionManager;
    private ConsoleCommandSender $consoleSender;
    private float $startTime;
    private int $currentTick = 0;
    private int $maxPlayers = self::DEFAULT_MAX_PLAYERS;
    private int $viewDistance = self::DEFAULT_MAX_VIEW_DISTANCE;
    private bool $running = true;
    private bool $whitelistEnabled = false;
    private string $serverUniqueId;
    private string $dataPath;
    private string $pluginPath;
    private string $resourcePath;
    /** @var array<string, bool> */
    private array $ops = [];
    /** @var array<string, bool> */
    private array $whitelist = [];
    /** @var array<string, Player> */
    private array $players = [];

    public function __construct(private ?ServerBridge $bridge = null)
    {
        self::$instance ??= $this;
        $this->commandMap = new SimpleCommandMap();
        $this->pluginManager = new PluginManager($this);
        $this->scheduler = new TaskScheduler('server');
        $this->tickSleeper = new TimeTrackingSleeperHandler();
        $this->asyncPool = new AsyncPool();
        $this->worldManager = new WorldManager($this, $this->dataPath ?? '');
        $this->logger = new MainLogger('server');
        $this->permissionManager = new PermissionManager();
        $this->consoleSender = new ConsoleCommandSender($this);
        $this->startTime = microtime(true);
        $this->serverUniqueId = bin2hex(random_bytes(16));
        $this->dataPath = rtrim(getcwd() ?: sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->pluginPath = $this->dataPath . 'plugins' . DIRECTORY_SEPARATOR;
        $this->resourcePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR;
        $this->worldManager = new WorldManager($this, $this->dataPath . 'worlds');
        $this->worldManager->setDefaultWorld(new World('world'));
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function getName(): string
    {
        return 'PocketMine-MP';
    }

    public function getMotd(): string
    {
        return self::DEFAULT_SERVER_NAME;
    }

    public function getPocketMineVersion(): string
    {
        return 'pmmpcompat';
    }

    public function getVersion(): string
    {
        return 'Bedrock';
    }

    public function getApiVersion(): string
    {
        return '5.0.0';
    }

    public function getFilePath(): string
    {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR;
    }

    public function getResourcePath(): string
    {
        return $this->resourcePath;
    }

    public function getDataPath(): string
    {
        return $this->dataPath;
    }

    public function getPluginPath(): string
    {
        return $this->pluginPath;
    }

    public function setPluginPath(string $pluginPath): void
    {
        $this->pluginPath = rtrim($pluginPath, '/\\') . DIRECTORY_SEPARATOR;
    }

    public function getMaxPlayers(): int
    {
        return $this->maxPlayers;
    }

    public function getAllowedViewDistance(int $distance = self::DEFAULT_MAX_VIEW_DISTANCE): int
    {
        return max(2, min($distance, $this->viewDistance));
    }

    public function getViewDistance(): int
    {
        return $this->viewDistance;
    }

    public function getIp(): string
    {
        return '0.0.0.0';
    }

    public function getIpV6(): string
    {
        return '::';
    }

    public function getPort(): int
    {
        return self::DEFAULT_PORT_IPV4;
    }

    public function getPortV6(): int
    {
        return self::DEFAULT_PORT_IPV6;
    }

    public function getOnlineMode(): bool
    {
        return true;
    }

    public function requiresAuthentication(): bool
    {
        return $this->getOnlineMode();
    }

    public function isHardcore(): bool
    {
        return false;
    }

    public function isRunning(): bool
    {
        return $this->running;
    }

    public function getStartTime(): float
    {
        return $this->startTime;
    }

    public function getServerUniqueId(): string
    {
        return $this->serverUniqueId;
    }

    public function getTick(): int
    {
        return $this->currentTick;
    }

    public function getTicksPerSecond(): float
    {
        return (float) self::TARGET_TICKS_PER_SECOND;
    }

    public function getTicksPerSecondAverage(): float
    {
        return (float) self::TARGET_TICKS_PER_SECOND;
    }

    public function getTickUsage(): float
    {
        return 0.0;
    }

    public function getTickUsageAverage(): float
    {
        return 0.0;
    }

    public function getPluginManager(): PluginManager
    {
        return $this->pluginManager;
    }

    public function getCommandMap(): SimpleCommandMap
    {
        return $this->commandMap;
    }

    public function getScheduler(): TaskScheduler
    {
        return $this->scheduler;
    }

    public function getLogger(): MainLogger
    {
        return $this->logger;
    }

    public function getPermissionManager(): PermissionManager
    {
        return $this->permissionManager;
    }

    public function getConsoleSender(): ConsoleCommandSender
    {
        return $this->consoleSender;
    }

    public function getPluginCommand(string $name): ?\pocketmine\command\PluginCommand
    {
        $command = $this->commandMap->getCommand($name);
        return $command instanceof \pocketmine\command\PluginCommand ? $command : null;
    }

    public function dispatchCommand(CommandSender $sender, string $commandLine): bool
    {
        $event = new CommandEvent($sender, '/' . ltrim($commandLine, '/'));
        $this->pluginManager->callEvent($event);
        if ($event->isCancelled()) {
            return true;
        }
        $parts = preg_split('/\s+/', ltrim($event->getCommand(), '/')) ?: [];
        $name = array_shift($parts) ?? '';
        $args = array_values(array_filter($parts, static fn(string $part): bool => $part !== ''));
        return $name !== '' && $this->commandMap->dispatch($sender, $name, $args);
    }

    public function getAsyncPool(): AsyncPool
    {
        return $this->asyncPool;
    }

    public function getTickSleeper(): TimeTrackingSleeperHandler
    {
        return $this->tickSleeper;
    }

    public function getConfigGroup(): never
    {
        $this->unsupported('server config group');
    }

    public function getCraftingManager(): never
    {
        $this->unsupported('crafting manager');
    }

    public function getIPBans(): never
    {
        $this->unsupported('IP ban list');
    }

    public function getLoader(): never
    {
        $this->unsupported('PocketMine bootstrap loader');
    }

    public function getNameBans(): never
    {
        $this->unsupported('name ban list');
    }

    public function getResourcePackManager(): never
    {
        $this->unsupported('resource pack manager');
    }

    public function getUpdater(): never
    {
        $this->unsupported('PocketMine updater');
    }

    public function getWorldManager(): WorldManager
    {
        return $this->worldManager;
    }

    /** @return array<string, string[]> */
    public function getCommandAliases(): array
    {
        $aliases = [];
        foreach ($this->commandMap->getCommands() as $label => $command) {
            if ($label !== strtolower($command->getName())) {
                $aliases[$command->getName()][] = $label;
            }
        }
        return $aliases;
    }

    public function getPlayerExact(string $name): ?Player
    {
        foreach ($this->players as $player) {
            if ($player->getName() === $name) {
                return $player;
            }
        }
        return null;
    }

    public function getPlayerByUUID(string|\Stringable $uuid): ?Player
    {
        return $this->players[(string) $uuid] ?? null;
    }

    public function getPlayerByRawUUID(string|\Stringable $uuid): ?Player
    {
        return $this->getPlayerByUUID($uuid);
    }

    public function getPlayerByPrefix(string $name): ?Player
    {
        $name = strtolower($name);
        foreach ($this->players as $player) {
            if (str_starts_with(strtolower($player->getName()), $name)) {
                return $player;
            }
        }
        return null;
    }

    public function getOfflinePlayer(string $name): ?Player
    {
        return $this->getPlayerExact($name);
    }

    /** @return Player[] */
    public function getOnlinePlayers(): array
    {
        return array_values($this->players);
    }

    public function broadcastMessage(string $message): void
    {
        if ($this->bridge !== null) {
            $this->bridge->broadcastMessage($message);
            return;
        }
        foreach ($this->players as $player) {
            $player->sendMessage($message);
        }
    }

    public function addPlayer(Player $player): void
    {
        $this->players[$player->getUniqueId()->toString()] = $player;
    }

    public function removePlayer(string|\Stringable $uuid): void
    {
        unset($this->players[(string) $uuid]);
    }

    public function tickSchedulers(int $currentTick): void
    {
        $this->currentTick = $currentTick;
        $this->scheduler->mainThreadHeartbeat($currentTick);
    }

    public function addOp(string $name): void
    {
        $this->ops[strtolower($name)] = true;
        $this->getPlayerExact($name)?->setOp(true);
    }

    public function removeOp(string $name): void
    {
        unset($this->ops[strtolower($name)]);
        $this->getPlayerExact($name)?->setOp(false);
    }

    public function isOp(string $name): bool
    {
        return $this->ops[strtolower($name)] ?? false;
    }

    /** @return string[] */
    public function getOps(): array
    {
        return array_keys($this->ops);
    }

    public function addWhitelist(string $name): void
    {
        $this->whitelist[strtolower($name)] = true;
    }

    public function removeWhitelist(string $name): void
    {
        unset($this->whitelist[strtolower($name)]);
    }

    public function isWhitelisted(string $name): bool
    {
        return !$this->whitelistEnabled || ($this->whitelist[strtolower($name)] ?? false);
    }

    public function hasWhitelist(): bool
    {
        return $this->whitelistEnabled;
    }

    /** @return string[] */
    public function getWhitelisted(): array
    {
        return array_keys($this->whitelist);
    }

    public function shouldSavePlayerData(): bool
    {
        return false;
    }

    public function hasOfflinePlayerData(string $name): bool
    {
        return false;
    }

    public function getOfflinePlayerData(string $name): mixed
    {
        return null;
    }

    public function saveOfflinePlayerData(string $name, mixed $nbtTag): void {}

    public function createPlayer(mixed ...$args): never
    {
        $this->unsupported('native player creation');
    }

    public function getDifficulty(): int
    {
        return 1;
    }

    public function getGamemode(): \pocketmine\player\GameMode
    {
        return \pocketmine\player\GameMode::SURVIVAL();
    }

    public function getForceGamemode(): bool
    {
        return false;
    }

    private function unsupported(string $feature): never
    {
        throw new \LogicException("PMMP {$feature} is not available in pmmpcompat; map this through the Dragonfly host adapter first.");
    }
}
