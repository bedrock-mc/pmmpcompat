<?php

declare(strict_types=1);

namespace pocketmine\block\tile;

class Beacon extends Tile
{
    public function __construct(mixed ...$args) { parent::__construct(...$args); }
    public function getPrimaryEffect(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function getSecondaryEffect(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function readSaveData(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function setPrimaryEffect(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function setSecondaryEffect(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
}
