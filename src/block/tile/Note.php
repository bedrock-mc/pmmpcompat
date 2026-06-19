<?php

declare(strict_types=1);

namespace pocketmine\block\tile;

class Note extends Tile
{
    public function __construct(mixed ...$args) { parent::__construct(...$args); }
    public function getPitch(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function readSaveData(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function setPitch(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
}
