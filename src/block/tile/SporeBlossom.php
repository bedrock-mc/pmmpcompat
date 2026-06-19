<?php

declare(strict_types=1);

namespace pocketmine\block\tile;

class SporeBlossom extends Tile
{
    public function __construct(mixed ...$args) { parent::__construct(...$args); }
    public function readSaveData(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
}
