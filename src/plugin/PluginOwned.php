<?php

declare(strict_types=1);

namespace pocketmine\plugin;

interface PluginOwned
{
    public function getOwningPlugin(): object;
}
