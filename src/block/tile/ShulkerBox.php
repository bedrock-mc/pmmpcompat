<?php

declare(strict_types=1);

namespace pocketmine\block\tile;

class ShulkerBox extends Tile
{
    public function __construct(mixed ...$args) { parent::__construct(...$args); }
    public const TAG_FACING = 0;
    public function close(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function copyDataFromItem(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function getCleanedNBT(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function getDefaultName(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function getFacing(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function getInventory(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function getRealInventory(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function readSaveData(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function setFacing(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
}
