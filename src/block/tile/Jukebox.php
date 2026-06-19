<?php

declare(strict_types=1);

namespace pocketmine\block\tile;

class Jukebox extends Tile
{
    public function __construct(mixed ...$args) { parent::__construct(...$args); }
    public function getRecord(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function readSaveData(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function setRecord(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
}
