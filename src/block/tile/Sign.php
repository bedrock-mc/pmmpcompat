<?php

declare(strict_types=1);

namespace pocketmine\block\tile;

class Sign extends Tile
{
    public function __construct(mixed ...$args) { parent::__construct(...$args); }
    public const TAG_BACK_TEXT = 0;
    public const TAG_FRONT_TEXT = 0;
    public const TAG_GLOWING_TEXT = 0;
    public const TAG_LEGACY_BUG_RESOLVE = 0;
    public const TAG_LOCKED_FOR_EDITING_BY = 0;
    public const TAG_PERSIST_FORMATTING = 0;
    public const TAG_TEXT_BLOB = 0;
    public const TAG_TEXT_COLOR = 0;
    public const TAG_TEXT_LINE = 0;
    public const TAG_WAXED = 0;
    public static function fixTextBlob(mixed ...$args): mixed { return self::compatTileStaticMethod(__FUNCTION__, $args); }
    public function getBackText(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function getEditorEntityRuntimeId(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function getText(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function isWaxed(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function readSaveData(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function setBackText(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function setEditorEntityRuntimeId(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function setText(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function setWaxed(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
}
