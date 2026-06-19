<?php

declare(strict_types=1);

namespace pocketmine\block\tile;

class TileFactory
{
    public function __construct(mixed ...$args) {}

    public static function createFromData(mixed ...$args): mixed { return Tile::compatTileStaticMethod(__FUNCTION__, $args); }
    public static function getSaveId(mixed ...$args): mixed { return Tile::compatTileStaticMethod(__FUNCTION__, $args); }
    public static function isRegistered(mixed ...$args): mixed { return Tile::compatTileStaticMethod(__FUNCTION__, $args); }
    public static function register(mixed ...$args): mixed { return Tile::compatTileStaticMethod(__FUNCTION__, $args); }
}
