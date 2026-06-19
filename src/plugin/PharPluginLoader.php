<?php

declare(strict_types=1);

namespace pocketmine\plugin;

use pocketmine\thread\ThreadSafeClassLoader;

class PharPluginLoader
{
    public function __construct(private ?ThreadSafeClassLoader $loader = null)
    {
    }

    public function canLoadPlugin(string $path): bool
    {
        return is_file($path) && str_ends_with(strtolower($path), '.phar');
    }

    public function loadPlugin(string $file): void
    {
        $description = $this->getPluginDescription($file);
        if ($description !== null && $this->loader !== null) {
            $this->loader->addPath($description->getSrcNamespacePrefix(), $this->getAccessProtocol() . $file . '/src');
        }
    }

    public function getPluginDescription(string $file): ?PluginDescription
    {
        if (!$this->canLoadPlugin($file) || !class_exists(\Phar::class)) {
            return null;
        }
        $phar = new \Phar($file);
        return $phar->offsetExists('plugin.yml') ? new PluginDescription(PluginDescription::parseString((string) $phar['plugin.yml']->getContent())) : null;
    }

    public function getAccessProtocol(): string
    {
        return 'phar://';
    }
}
