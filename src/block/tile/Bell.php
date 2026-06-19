<?php

declare(strict_types=1);

namespace pocketmine\block\tile;

class Bell extends Tile
{
    public function __construct(mixed ...$args) { parent::__construct(...$args); }
    public const TAG_DIRECTION = 0;
    public const TAG_RINGING = 0;
    public const TAG_TICKS = 0;
    public function createFakeUpdatePacket(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function getFacing(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function getTicks(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function isRinging(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function readSaveData(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function setFacing(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function setRinging(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function setTicks(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
}
