<?php

declare(strict_types=1);

namespace pocketmine\block\tile;

class Bed extends Tile
{
    public function __construct(mixed ...$args) { parent::__construct(...$args); }
    public const TAG_COLOR = 0;
    public function getColor(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function readSaveData(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function setColor(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
}
