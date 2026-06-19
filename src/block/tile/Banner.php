<?php

declare(strict_types=1);

namespace pocketmine\block\tile;

class Banner extends Tile
{
    public function __construct(mixed ...$args) { parent::__construct(...$args); }
    public const TAG_BASE = 0;
    public const TAG_PATTERNS = 0;
    public const TAG_PATTERN_COLOR = 0;
    public const TAG_PATTERN_NAME = 0;
    public const TAG_TYPE = 0;
    public const TYPE_NORMAL = 0;
    public const TYPE_OMINOUS = 0;
    public function getBaseColor(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function getDefaultName(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function getPatterns(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function getType(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function readSaveData(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function setBaseColor(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function setPatterns(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function setType(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
}
