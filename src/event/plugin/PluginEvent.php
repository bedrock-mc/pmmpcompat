<?php

declare(strict_types=1);

namespace pocketmine\event\plugin;

use pocketmine\event\Event;
use pocketmine\plugin\Plugin;

abstract class PluginEvent extends Event
{
    public function __construct(private Plugin $plugin) {}

    public function getPlugin(): Plugin
    {
        return $this->plugin;
    }
}
