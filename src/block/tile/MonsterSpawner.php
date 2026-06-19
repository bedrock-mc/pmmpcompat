<?php

declare(strict_types=1);

namespace pocketmine\block\tile;

class MonsterSpawner extends Tile
{
    public function __construct(mixed ...$args) { parent::__construct(...$args); }
    public const DEFAULT_MAX_NEARBY_ENTITIES = 0;
    public const DEFAULT_MAX_SPAWN_DELAY = 0;
    public const DEFAULT_MIN_SPAWN_DELAY = 0;
    public const DEFAULT_REQUIRED_PLAYER_RANGE = 0;
    public const DEFAULT_SPAWN_RANGE = 0;
    public function readSaveData(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
}
