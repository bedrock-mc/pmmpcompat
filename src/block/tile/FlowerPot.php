<?php

declare(strict_types=1);

namespace pocketmine\block\tile;

class FlowerPot extends Tile
{
    public function __construct(mixed ...$args) { parent::__construct(...$args); }
    public function getPlant(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function getRenderUpdateBugWorkaroundStateProperties(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function readSaveData(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function setPlant(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
}
