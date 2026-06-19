<?php

declare(strict_types=1);

namespace pocketmine\block\tile;

class Chest extends Tile
{
    public function __construct(mixed ...$args) { parent::__construct(...$args); }
    public const TAG_PAIRX = 0;
    public const TAG_PAIRZ = 0;
    public const TAG_PAIR_LEAD = 0;
    public function close(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function getCleanedNBT(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function getDefaultName(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function getInventory(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function getPair(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function getRealInventory(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function isPaired(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function pairWith(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function readSaveData(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function unpair(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
}
