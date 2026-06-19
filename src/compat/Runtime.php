<?php

declare(strict_types=1);

namespace pocketmine\compat;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginLoader;
use pocketmine\Server;

/**
 * Transport-neutral PMMP plugin host.
 *
 * Host integrations should translate their own server events into these plain
 * methods. This class intentionally has no host transport dependency.
 */
class Runtime
{
    /** @var PluginBase[] */
    private array $plugins = [];
    private PluginLoader $loader;
    private Server $server;

    public function __construct(private string $pluginsDir, ?Server $server = null)
    {
        $this->server = $server ?? new Server();
        $this->server->setPluginPath($pluginsDir);
        $this->loader = new PluginLoader($this->server);
    }

    public function load(): void
    {
        $this->plugins = $this->loader->loadDirectory($this->pluginsDir);
        foreach ($this->plugins as $plugin) {
            $plugin->__pmmpCallLoad();
        }
    }

    public function enable(): void
    {
        foreach ($this->plugins as $plugin) {
            $plugin->__pmmpCallEnable();
        }
    }

    public function disable(): void
    {
        for ($i = count($this->plugins) - 1; $i >= 0; $i--) {
            $this->plugins[$i]->__pmmpCallDisable();
        }
    }

    public function server(): Server
    {
        return $this->server;
    }

    /** @return PluginBase[] */
    public function plugins(): array
    {
        return $this->plugins;
    }

    public function playerJoin(string $uuid, string $name, ?PlayerBridge $bridge = null): PlayerJoinEvent
    {
        $player = new Player($uuid, $name, $bridge);
        $this->server->addPlayer($player);
        $event = new PlayerJoinEvent($player, "{$player->getName()} joined the game");
        $this->server->getPluginManager()->callEvent($event);
        return $event;
    }

    public function playerQuit(string $uuid, string $name): PlayerQuitEvent
    {
        $player = $this->server->getPlayerByUUID($uuid) ?? new Player($uuid, $name);
        $event = new PlayerQuitEvent($player, "{$player->getName()} left the game");
        $this->server->getPluginManager()->callEvent($event);
        $this->server->removePlayer($uuid);
        return $event;
    }

    public function chat(string $uuid, string $name, string $message): PlayerChatEvent
    {
        $player = $this->server->getPlayerByUUID($uuid) ?? new Player($uuid, $name);
        $event = new PlayerChatEvent($player, $message);
        $this->server->getPluginManager()->callEvent($event);
        return $event;
    }

    /** @param string[] $args */
    public function command(string $uuid, string $name, string $command, array $args = []): bool
    {
        $player = $this->server->getPlayerByUUID($uuid) ?? new Player($uuid, $name);
        $message = '/' . $command;
        foreach ($args as $arg) {
            $message .= ' ' . $arg;
        }
        $preprocess = new PlayerCommandPreprocessEvent($player, $message);
        $this->server->getPluginManager()->callEvent($preprocess);
        if ($preprocess->isCancelled()) {
            return true;
        }

        $parts = preg_split('/\s+/', ltrim($preprocess->getMessage(), '/')) ?: [];
        $command = array_shift($parts) ?? '';
        $dispatchArgs = [];
        foreach ($parts as $arg) {
            if ($arg !== '') {
                $dispatchArgs[] = $arg;
            }
        }
        return $command !== '' && $this->server->getCommandMap()->dispatch($player, $command, $dispatchArgs);
    }

    public function playerMove(string $uuid, string $name, Vector3 $to): PlayerMoveEvent
    {
        $player = $this->server->getPlayerByUUID($uuid) ?? new Player($uuid, $name);
        $event = new PlayerMoveEvent($player, $player->getPosition(), $to);
        $this->server->getPluginManager()->callEvent($event);
        return $event;
    }

    public function blockBreak(string $uuid, string $name, Vector3 $position): BlockBreakEvent
    {
        $player = $this->server->getPlayerByUUID($uuid) ?? new Player($uuid, $name);
        $event = new BlockBreakEvent($player, $position);
        $this->server->getPluginManager()->callEvent($event);
        return $event;
    }

    public function blockPlace(string $uuid, string $name, Vector3 $position): BlockPlaceEvent
    {
        $player = $this->server->getPlayerByUUID($uuid) ?? new Player($uuid, $name);
        $event = new BlockPlaceEvent($player, $position);
        $this->server->getPluginManager()->callEvent($event);
        return $event;
    }

    public function formResponse(string $uuid, int $formId, mixed $data): bool
    {
        $player = $this->server->getPlayerByUUID($uuid);
        return $player !== null && $player->handleFormResponse($formId, $data);
    }

    /** @param array<int, Item> $slots */
    public function syncPlayerInventory(string $uuid, array $slots): bool
    {
        $player = $this->server->getPlayerByUUID($uuid);
        if ($player === null) {
            return false;
        }
        $player->getInventory()->replaceContents($slots, false);
        return true;
    }

    /** @param array<string, mixed> $state */
    public function syncPlayerState(string $uuid, array $state): bool
    {
        $player = $this->server->getPlayerByUUID($uuid);
        if ($player === null) {
            return false;
        }
        if (isset($state['position']) && $state['position'] instanceof Vector3) {
            $position = $state['position'];
            $player->syncPosition($position instanceof \pocketmine\world\Position ? $position : new \pocketmine\world\Position($position->x, $position->y, $position->z, $player->getWorld()));
        }
        if (isset($state['health']) || isset($state['max_health'])) {
            $player->syncHealth((float) ($state['health'] ?? $player->getHealth()), (float) ($state['max_health'] ?? $player->getMaxHealth()));
        }
        if (isset($state['gamemode'])) {
            $player->syncGamemode($state['gamemode'] instanceof GameMode ? $state['gamemode'] : GameMode::fromString((string) $state['gamemode']));
        }
        if (isset($state['xp_level']) || isset($state['xp_progress'])) {
            $player->syncExperience((int) ($state['xp_level'] ?? $player->getXpLevel()), (float) ($state['xp_progress'] ?? $player->getXpProgress()));
        }
        return true;
    }

    public function tick(int $currentTick): void
    {
        $this->server->tickSchedulers($currentTick);
        foreach ($this->plugins as $plugin) {
            $plugin->__pmmpTickScheduler($currentTick);
        }
    }
}
