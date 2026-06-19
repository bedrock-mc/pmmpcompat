<?php

declare(strict_types=1);

namespace pocketmine\block\tile;

use pocketmine\inventory\Inventory;

class BrewingStand extends \pocketmine\block\Block
{
    public const BREW_TIME_TICKS = 0;

    private Inventory $inventory;
    private bool $closed = false;

    public function __construct(mixed ...$args)
    {
        parent::__construct('minecraft:brewing_stand', 'Brewing Stand');
        $this->inventory = new Inventory(5);
    }

    public function close(mixed ...$args): mixed { $this->closed = true; return null; }
    public function getBlock(mixed ...$args): mixed { return $this; }
    public function getDefaultName(mixed ...$args): mixed { return 'Brewing Stand'; }
    public function getInventory(mixed ...$args): mixed { return $this->inventory; }
    public function getRealInventory(mixed ...$args): mixed { return $this->inventory; }
    public function isClosed(mixed ...$args): mixed { return $this->closed; }
    public function onUpdate(mixed ...$args): mixed { return false; }
    public function readSaveData(mixed ...$args): mixed { return null; }
}
