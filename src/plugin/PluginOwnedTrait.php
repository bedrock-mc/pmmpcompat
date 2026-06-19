<?php

declare(strict_types=1);

namespace pocketmine\plugin;

trait PluginOwnedTrait
{
    private object $owningPlugin;

    public function __construct(object $owningPlugin)
    {
        $this->owningPlugin = $owningPlugin;
    }

    public function getOwningPlugin(): object
    {
        return $this->owningPlugin;
    }
}
