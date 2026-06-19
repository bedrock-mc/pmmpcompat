<?php

declare(strict_types=1);

namespace pocketmine\plugin;

use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;
use pocketmine\utils\Config;

abstract class PluginBase implements CommandExecutor, Plugin
{
    private bool $enabled = false;
    private ?Server $server = null;
    private ?PluginLoader $loader = null;
    private ?PluginDescription $description = null;
    private string $dataFolder = '';
    private string $resourceFolder = '';
    private ?TaskScheduler $scheduler = null;
    private ?PluginLogger $logger = null;
    private ?Config $config = null;

    public function __construct() {}

    final public function __pmmpInit(Server $server, PluginDescription $description, string $dataFolder, string $resourceFolder = '', ?PluginLoader $loader = null): void
    {
        $this->server = $server;
        $this->loader = $loader;
        $this->description = $description;
        $this->dataFolder = rtrim($dataFolder, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->resourceFolder = $resourceFolder === '' ? '' : rtrim($resourceFolder, '/\\') . DIRECTORY_SEPARATOR;
        $this->scheduler = new TaskScheduler($description->getName());
        $this->logger = new PluginLogger($description->getName());
        if (!is_dir($this->dataFolder)) {
            mkdir($this->dataFolder, 0777, true);
        }
    }

    final public function __pmmpCallLoad(): void
    {
        $this->onLoad();
    }

    final public function __pmmpCallEnable(): void
    {
        if (!$this->enabled) {
            $this->enabled = true;
            foreach ($this->getDescription()->getPermissions() as $name => $spec) {
                $this->getServer()->getPermissionManager()->addPermission(new Permission((string) $name, is_array($spec) ? (string) ($spec['description'] ?? '') : ''));
            }
            foreach ($this->getDescription()->getCommandMap() as $name => $spec) {
                $this->getServer()->getCommandMap()->register((string) $name, new \pocketmine\command\PluginCommand((string) $name, $this, is_array($spec) ? $spec : []));
            }
            $this->onEnable();
        }
    }

    final public function __pmmpCallDisable(): void
    {
        if ($this->enabled) {
            $this->enabled = false;
            $this->scheduler?->cancelAllTasks();
            $this->onDisable();
        }
    }

    final public function onEnableStateChange(bool $enabled): void
    {
        if ($enabled) {
            $this->__pmmpCallEnable();
        } else {
            $this->__pmmpCallDisable();
        }
    }

    protected function onLoad(): void {}
    protected function onEnable(): void {}
    protected function onDisable(): void {}

    public function getServer(): Server
    {
        return $this->server ?? throw new \LogicException('Plugin not initialized.');
    }

    public function getPluginLoader(): PluginLoader
    {
        return $this->loader ?? throw new \LogicException('Plugin not initialized.');
    }

    public function getDescription(): PluginDescription
    {
        return $this->description ?? throw new \LogicException('Plugin not initialized.');
    }

    public function getName(): string
    {
        return $this->getDescription()->getName();
    }

    public function getFullName(): string
    {
        return $this->getName() . ' v' . $this->getDescription()->getVersion();
    }

    public function getCommand(string $name): ?\pocketmine\command\PluginCommand
    {
        $command = $this->getServer()->getCommandMap()->getCommand($name);
        return $command instanceof \pocketmine\command\PluginCommand && $command->getOwningPlugin() === $this ? $command : null;
    }

    public function getDataFolder(): string
    {
        return $this->dataFolder;
    }

    public function getResourceFolder(): string
    {
        return $this->resourceFolder;
    }

    public function getResourcePath(string $filename): string
    {
        return $this->resourceFolder . $this->normaliseResourcePath($filename);
    }

    public function getResource(string $filename): mixed
    {
        $path = $this->getResourcePath($filename);
        if ($this->resourceFolder === '' || !is_file($path)) {
            return null;
        }
        $resource = fopen($path, 'rb');
        return $resource === false ? null : $resource;
    }

    /** @return array<string, \SplFileInfo> */
    public function getResources(): array
    {
        if ($this->resourceFolder === '' || !is_dir($this->resourceFolder)) {
            return [];
        }
        $resources = [];
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->resourceFolder));
        foreach ($files as $file) {
            if (!$file instanceof \SplFileInfo || !$file->isFile()) {
                continue;
            }
            $path = str_replace('\\', '/', substr($file->getPathname(), strlen($this->resourceFolder)));
            $resources[$path] = $file;
        }
        return $resources;
    }

    public function getScheduler(): TaskScheduler
    {
        return $this->scheduler ?? throw new \LogicException('Plugin not initialized.');
    }

    public function getLogger(): PluginLogger
    {
        return $this->logger ?? throw new \LogicException('Plugin not initialized.');
    }

    public function getConfig(): Config
    {
        return $this->config ??= new Config($this->dataFolder . 'config.yml', Config::YAML);
    }

    public function saveResource(string $filename, bool $replace = false): bool
    {
        $filename = $this->normaliseResourcePath($filename);
        if ($filename === '') {
            return false;
        }
        $source = $this->getResourcePath($filename);
        if ($this->resourceFolder === '' || !is_file($source)) {
            return false;
        }
        $destination = $this->dataFolder . str_replace('/', DIRECTORY_SEPARATOR, $filename);
        if (is_file($destination) && !$replace) {
            return false;
        }
        $dir = dirname($destination);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        return copy($source, $destination);
    }

    /** @param array<string, mixed> $defaults */
    public function saveDefaultConfig(array $defaults = []): bool
    {
        $path = $this->dataFolder . 'config.yml';
        if (is_file($path)) {
            return false;
        }
        if ($this->saveResource('config.yml', false)) {
            return true;
        }
        if ($defaults !== []) {
            (new Config($path, Config::YAML, $defaults))->save();
            return true;
        }
        return false;
    }

    public function saveConfig(): void
    {
        $this->getConfig()->save();
    }

    public function reloadConfig(): void
    {
        $this->saveDefaultConfig();
        $this->config = new Config($this->dataFolder . 'config.yml', Config::YAML);
    }

    /** Advances this plugin's scheduler. The host runtime calls this once per server tick. */
    final public function __pmmpTickScheduler(int $currentTick): void
    {
        $this->scheduler?->mainThreadHeartbeat($currentTick);
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function isDisabled(): bool
    {
        return !$this->enabled;
    }

    /** @param string[] $args */
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        return false;
    }

    private function normaliseResourcePath(string $filename): string
    {
        $filename = trim(str_replace('\\', '/', $filename), '/');
        if ($filename === '' || str_contains($filename, '../') || $filename === '..') {
            return '';
        }
        return $filename;
    }
}
