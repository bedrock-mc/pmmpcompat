<?php

declare(strict_types=1);

namespace pocketmine\network\query;

use function array_map;
use function chr;
use function count;
use function method_exists;
use function pack;
use function str_replace;
use function substr;

final class QueryInfo
{
    public const GAME_ID = 'MINECRAFTPE';

    private string $serverName = 'Minecraft Server';
    private bool $listPlugins = true;
    /** @var object[] */
    private array $plugins = [];
    /** @var string[] */
    private array $players = [];
    private string $gametype = 'SMP';
    private string $version = '';
    private string $serverEngine = 'PocketMine-MP';
    private string $map = 'unknown';
    private int $numPlayers = 0;
    private int $maxPlayers = 0;
    private string $whitelist = 'off';
    private int $port = 19132;
    private string $ip = '0.0.0.0';
    /** @var array<string, string> */
    private array $extraData = [];
    private ?string $longQueryCache = null;
    private ?string $shortQueryCache = null;

    public function __construct(mixed $server = null)
    {
        if ($server !== null) {
            $this->readServerState($server);
        }
    }

    private function readServerState(mixed $server): void
    {
        if (is_object($server) && method_exists($server, 'getMotd')) {
            $this->serverName = (string) $server->getMotd();
        }
        if (is_object($server) && method_exists($server, 'getPluginManager')) {
            $pluginManager = $server->getPluginManager();
            if (is_object($pluginManager) && method_exists($pluginManager, 'getPlugins')) {
                $plugins = $pluginManager->getPlugins();
                $this->plugins = is_array($plugins) ? $plugins : [];
            }
        }
        if (is_object($server) && method_exists($server, 'getOnlinePlayers')) {
            $players = $server->getOnlinePlayers();
            if (is_array($players)) {
                $this->players = array_map(static fn(mixed $player): string => is_object($player) && method_exists($player, 'getName') ? (string) $player->getName() : (string) $player, $players);
                $this->numPlayers = count($this->players);
            }
        }
        if (is_object($server) && method_exists($server, 'getVersion')) {
            $this->version = (string) $server->getVersion();
        }
        if (is_object($server) && method_exists($server, 'getName')) {
            $this->serverEngine = (string) $server->getName();
            if (method_exists($server, 'getPocketMineVersion')) {
                $this->serverEngine .= ' ' . (string) $server->getPocketMineVersion();
            }
        }
        if (is_object($server) && method_exists($server, 'getMaxPlayers')) {
            $this->maxPlayers = (int) $server->getMaxPlayers();
        }
        if (is_object($server) && method_exists($server, 'hasWhitelist')) {
            $this->whitelist = $server->hasWhitelist() ? 'on' : 'off';
        }
        if (is_object($server) && method_exists($server, 'getPort')) {
            $this->port = (int) $server->getPort();
        }
        if (is_object($server) && method_exists($server, 'getIp')) {
            $this->ip = (string) $server->getIp();
        }
    }

    private function destroyCache(): void
    {
        $this->longQueryCache = null;
        $this->shortQueryCache = null;
    }

    public function getServerName(): string { return $this->serverName; }
    public function setServerName(string $serverName): void { $this->serverName = $serverName; $this->destroyCache(); }
    public function canListPlugins(): bool { return $this->listPlugins; }
    public function setListPlugins(bool $value): void { $this->listPlugins = $value; $this->destroyCache(); }
    /** @return object[] */
    public function getPlugins(): array { return $this->plugins; }
    /** @param object[] $plugins */
    public function setPlugins(array $plugins): void { $this->plugins = $plugins; $this->destroyCache(); }
    /** @return string[] */
    public function getPlayerList(): array { return $this->players; }
    /** @param string[] $players */
    public function setPlayerList(array $players): void { $this->players = array_map('strval', $players); $this->destroyCache(); }
    public function getPlayerCount(): int { return $this->numPlayers; }
    public function setPlayerCount(int $count): void { $this->numPlayers = $count; $this->destroyCache(); }
    public function getMaxPlayerCount(): int { return $this->maxPlayers; }
    public function setMaxPlayerCount(int $count): void { $this->maxPlayers = $count; $this->destroyCache(); }
    public function getWorld(): string { return $this->map; }
    public function setWorld(string $world): void { $this->map = $world; $this->destroyCache(); }
    /** @return array<string, string> */
    public function getExtraData(): array { return $this->extraData; }
    /** @param array<string, string> $extraData */
    public function setExtraData(array $extraData): void { $this->extraData = array_map('strval', $extraData); $this->destroyCache(); }

    public function getLongQuery(): string
    {
        if ($this->longQueryCache !== null) {
            return $this->longQueryCache;
        }

        $pluginList = $this->serverEngine;
        if (count($this->plugins) > 0 && $this->listPlugins) {
            $pluginList .= ':';
            foreach ($this->plugins as $plugin) {
                $description = method_exists($plugin, 'getDescription') ? $plugin->getDescription() : $plugin;
                $name = is_object($description) && method_exists($description, 'getName') ? $description->getName() : (string) $description;
                $version = is_object($description) && method_exists($description, 'getVersion') ? $description->getVersion() : '';
                $pluginList .= ' ' . $this->sanitizePluginField((string) $name) . ' ' . $this->sanitizePluginField((string) $version) . ';';
            }
            $pluginList = substr($pluginList, 0, -1);
        }

        $query = '';
        $data = [
            'splitnum' => chr(128),
            'hostname' => $this->serverName,
            'gametype' => $this->gametype,
            'game_id' => self::GAME_ID,
            'version' => $this->version,
            'server_engine' => $this->serverEngine,
            'plugins' => $pluginList,
            'map' => $this->map,
            'numplayers' => $this->numPlayers,
            'maxplayers' => $this->maxPlayers,
            'whitelist' => $this->whitelist,
            'hostip' => $this->ip,
            'hostport' => $this->port,
        ];
        foreach ($data + $this->extraData as $key => $value) {
            $query .= $key . "\x00" . $value . "\x00";
        }
        $query .= "\x00\x01player_\x00\x00";
        foreach ($this->players as $player) {
            $query .= $player . "\x00";
        }
        return $this->longQueryCache = $query . "\x00";
    }

    public function getShortQuery(): string
    {
        return $this->shortQueryCache ??= $this->serverName . "\x00" . $this->gametype . "\x00" . $this->map . "\x00" . $this->numPlayers . "\x00" . $this->maxPlayers . "\x00" . pack('v', $this->port) . $this->ip . "\x00";
    }

    private function sanitizePluginField(string $value): string
    {
        return str_replace([';', ':', ' '], ['', '', '_'], $value);
    }
}
