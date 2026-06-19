<?php

declare(strict_types=1);

namespace pocketmine\block\tile;

use pocketmine\inventory\Inventory;

class Furnace extends \pocketmine\block\Block
{
    public const TAG_BURN_TIME = 0;
    public const TAG_COOK_TIME = 0;
    public const TAG_MAX_TIME = 0;

    private Inventory $inventory;
    private bool $closed = false;

    public function __construct(mixed ...$args)
    {
        parent::__construct('minecraft:furnace', 'Furnace');
        $this->inventory = new Inventory(3);
    }

    public function close(mixed ...$args): mixed { $this->closed = true; return null; }
    public function getBlock(mixed ...$args): mixed { return $this; }
    public function getDefaultName(mixed ...$args): mixed { return 'Furnace'; }
    public function getFurnaceType(mixed ...$args): mixed { return 'furnace'; }
    public function getInventory(mixed ...$args): mixed { return $this->inventory; }
    public function getRealInventory(mixed ...$args): mixed { return $this->inventory; }
    public function isClosed(mixed ...$args): mixed { return $this->closed; }
    public function onUpdate(mixed ...$args): mixed { return false; }
    public function readSaveData(mixed ...$args): mixed { return null; }
}
