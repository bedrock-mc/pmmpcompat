<?php

declare(strict_types=1);

namespace pocketmine\plugin;

interface Plugin
{
    public function __construct();
    public function isEnabled(): bool;
    public function onEnableStateChange(bool $enabled): void;
    public function getDataFolder(): string;
    public function getDescription(): PluginDescription;
    public function getFile(): string;
    public function getName(): string;
    public function getLogger(): mixed;
    public function getPluginLoader(): PluginLoader;
    public function getScheduler(): mixed;
}
