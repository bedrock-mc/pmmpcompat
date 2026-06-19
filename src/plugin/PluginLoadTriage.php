<?php

declare(strict_types=1);

namespace pocketmine\plugin;

final class PluginLoadTriage
{
    /** @var array<string, PluginLoadTriageEntry> */
    public array $plugins = [];

    /** @var array<string, list<string>> */
    public array $dependencies = [];

    /** @var array<string, list<string>> */
    public array $softDependencies = [];
}
