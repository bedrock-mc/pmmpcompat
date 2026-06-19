<?php

declare(strict_types=1);

namespace pocketmine\block\tile;

class Spawnable extends Tile
{
    public function __construct(mixed ...$args) { parent::__construct(...$args); }
    public function clearSpawnCompoundCache(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function getRenderUpdateBugWorkaroundStateProperties(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function getSerializedSpawnCompound(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function getSpawnCompound(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function isDirty(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function setDirty(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
}
