<?php

declare(strict_types=1);

namespace pocketmine\plugin;

/**
 * @internal
 */
final class PluginLoadTriageEntry
{
    public function __construct(
        private string $file,
        private PluginLoader $loader,
        private PluginDescription $description
    ) {
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getLoader(): PluginLoader
    {
        return $this->loader;
    }

    public function getDescription(): PluginDescription
    {
        return $this->description;
    }
}
