<?php

declare(strict_types=1);

namespace pocketmine\plugin;

use pocketmine\Server;
use pocketmine\thread\ThreadSafeClassLoader;

class PluginLoader
{
    public function __construct(private Server $server) {}

    public function canLoadPlugin(string $path): bool
    {
        return (is_dir($path) && $this->pluginYmlPath($path) !== null) || (is_file($path) && str_ends_with(strtolower($path), '.phar'));
    }

    public function loadPlugin(string $file): void
    {
        $this->loadPath($file);
    }

    public function getPluginDescription(string $file): ?PluginDescription
    {
        if (is_dir($file) && ($pluginYml = $this->pluginYmlPath($file)) !== null) {
            return PluginDescription::fromFile($pluginYml);
        }
        if (is_file($file) && str_ends_with(strtolower($file), '.phar') && class_exists(\Phar::class)) {
            $phar = new \Phar($file);
            $pluginYml = $this->pharPluginYmlPath($phar);
            return $pluginYml !== null ? new PluginDescription(PluginDescription::parseString((string) $phar[$pluginYml]->getContent())) : null;
        }
        return null;
    }

    public function getAccessProtocol(): string
    {
        return 'file';
    }

    /** @return PluginBase[] */
    public function loadDirectory(string $pluginsDir): array
    {
        if (!is_dir($pluginsDir)) {
            return [];
        }
        $plugins = [];
        foreach (scandir($pluginsDir) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $path = $pluginsDir . DIRECTORY_SEPARATOR . $entry;
            if (is_dir($path) && $this->pluginYmlPath($path) !== null) {
                $plugins[] = $this->loadFolder($path, false);
            } elseif (is_file($path) && str_ends_with(strtolower($path), '.phar')) {
                $plugins[] = $this->loadPhar($path, false);
            }
        }
        return $this->sortAndRegister($plugins);
    }

    public function loadPath(string $path): PluginBase
    {
        if (is_dir($path)) {
            return $this->loadFolder($path);
        }
        if (is_file($path) && str_ends_with(strtolower($path), '.phar')) {
            return $this->loadPhar($path);
        }
        throw new \RuntimeException("Unsupported plugin path: {$path}");
    }

    public function loadFolder(string $path, bool $register = true): PluginBase
    {
        $pluginYml = $this->pluginYmlPath($path);
        if ($pluginYml === null) {
            throw new \RuntimeException("plugin.yml not found in plugin folder: {$path}");
        }
        $description = PluginDescription::fromFile($pluginYml);
        $main = $description->getMain();
        if ($main === '') {
            throw new \RuntimeException("Plugin {$path} has no main class.");
        }
        $src = $path . DIRECTORY_SEPARATOR . 'src';
        if (is_dir($src)) {
            $this->registerSourceAutoloader($src, $description);
        }
        $vendorAutoload = $path . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
        if (is_file($vendorAutoload)) {
            require_once $vendorAutoload;
        }
        if (!class_exists($main)) {
            throw new \RuntimeException("Plugin main class not found: {$main}");
        }
        $plugin = new $main();
        if (!$plugin instanceof PluginBase) {
            throw new \RuntimeException("Plugin main class must extend " . PluginBase::class);
        }
        $plugin->__pmmpInit($this->server, $description, $path . DIRECTORY_SEPARATOR . 'plugin_data', $path . DIRECTORY_SEPARATOR . 'resources', $this, $path);
        if ($register) {
            $this->server->getPluginManager()->registerPlugin($plugin);
        }
        return $plugin;
    }

    public function loadPhar(string $path, bool $register = true): PluginBase
    {
        if (!class_exists(\Phar::class)) {
            throw new \RuntimeException('PHP Phar extension is required to load PMMP phar plugins.');
        }
        $phar = new \Phar($path);
        $pluginYml = $this->pharPluginYmlPath($phar);
        if ($pluginYml === null) {
            throw new \RuntimeException("plugin.yml not found in phar: {$path}");
        }
        $description = new PluginDescription(PluginDescription::parseString((string) $phar[$pluginYml]->getContent()));
        $prefix = 'phar://' . $path . '/src';
        if (is_dir($prefix)) {
            $this->registerSourceAutoloader($prefix, $description);
        }
        $main = $description->getMain();
        if (!class_exists($main)) {
            throw new \RuntimeException("Plugin main class not found: {$main}");
        }
        $plugin = new $main();
        if (!$plugin instanceof PluginBase) {
            throw new \RuntimeException("Plugin main class must extend " . PluginBase::class);
        }
        $dataRoot = dirname($path) . DIRECTORY_SEPARATOR . pathinfo($path, PATHINFO_FILENAME) . '_data';
        $plugin->__pmmpInit($this->server, $description, $dataRoot, 'phar://' . $path . '/resources', $this, $path);
        if ($register) {
            $this->server->getPluginManager()->registerPlugin($plugin);
        }
        return $plugin;
    }

    private function pluginYmlPath(string $path): ?string
    {
        foreach (['plugin.yml', 'Plugin.yml', 'plugin.yaml', 'Plugin.yaml'] as $name) {
            $candidate = $path . DIRECTORY_SEPARATOR . $name;
            if (is_file($candidate)) {
                return $candidate;
            }
        }
        return null;
    }

    private function pharPluginYmlPath(\Phar $phar): ?string
    {
        foreach (['plugin.yml', 'Plugin.yml', 'plugin.yaml', 'Plugin.yaml'] as $name) {
            if ($phar->offsetExists($name)) {
                return $name;
            }
        }
        return null;
    }

    private function registerSourceAutoloader(string $src, PluginDescription $description): void
    {
        $prefix = trim($description->getSrcNamespacePrefix(), '\\');
        $prefix = $prefix === '' ? '' : $prefix . '\\';
        $root = rtrim($src, '/\\');
        ThreadSafeClassLoader::getDefault()->addPath($prefix, $root);

        spl_autoload_register(static function (string $class) use ($root, $prefix): void {
            $trimmed = ltrim($class, '\\');
            if ($prefix !== '' && !str_starts_with($trimmed, $prefix)) {
                return;
            }
            $relativePaths = [$trimmed];
            if ($prefix !== '') {
                $relativePaths[] = substr($trimmed, strlen($prefix));
            }
            foreach (array_unique($relativePaths) as $relative) {
                $file = $root . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';
                if (is_file($file)) {
                    require_once $file;
                    return;
                }
            }
        });
    }

    /** @param PluginBase[] $plugins @return PluginBase[] */
    private function sortAndRegister(array $plugins): array
    {
        $byName = [];
        foreach ($plugins as $plugin) {
            $byName[strtolower($plugin->getName())] = $plugin;
        }

        $sorted = [];
        $visiting = [];
        $visited = [];
        $visit = function (PluginBase $plugin) use (&$visit, &$sorted, &$visiting, &$visited, $byName): void {
            $name = strtolower($plugin->getName());
            if (isset($visited[$name])) {
                return;
            }
            if (isset($visiting[$name])) {
                throw new \RuntimeException("Circular plugin dependency involving {$plugin->getName()}");
            }
            $visiting[$name] = true;
            $dependencies = array_merge($plugin->getDescription()->getDepend(), $plugin->getDescription()->getSoftDepend());
            foreach ($byName as $other) {
                foreach ($other->getDescription()->getLoadBefore() as $before) {
                    if (strtolower($before) === $name) {
                        $dependencies[] = $other->getName();
                    }
                }
            }
            foreach ($dependencies as $dep) {
                $depKey = strtolower($dep);
                if (isset($byName[$depKey])) {
                    $visit($byName[$depKey]);
                } elseif (in_array($dep, $plugin->getDescription()->getDepend(), true)) {
                    throw new \RuntimeException("Plugin {$plugin->getName()} depends on missing plugin {$dep}");
                }
            }
            unset($visiting[$name]);
            $visited[$name] = true;
            $sorted[] = $plugin;
        };

        foreach ($plugins as $plugin) {
            $visit($plugin);
        }
        foreach ($sorted as $plugin) {
            $this->server->getPluginManager()->registerPlugin($plugin);
        }
        return $sorted;
    }
}
