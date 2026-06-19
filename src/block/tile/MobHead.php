<?php

declare(strict_types=1);

namespace pocketmine\block\tile;

class MobHead extends Tile
{
    public function __construct(mixed ...$args) { parent::__construct(...$args); }
    public function getMobHeadType(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function getRotation(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function readSaveData(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function setMobHeadType(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function setRotation(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
}
