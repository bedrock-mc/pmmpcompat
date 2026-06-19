<?php

declare(strict_types=1);

namespace pocketmine\block\tile;

class ChiseledBookshelf extends Tile
{
    public function __construct(mixed ...$args) { parent::__construct(...$args); }
    public function getInventory(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function getLastInteractedSlot(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function getRealInventory(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function readSaveData(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function setLastInteractedSlot(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
}
