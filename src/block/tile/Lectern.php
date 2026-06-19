<?php

declare(strict_types=1);

namespace pocketmine\block\tile;

class Lectern extends Tile
{
    public function __construct(mixed ...$args) { parent::__construct(...$args); }
    public const TAG_BOOK = 0;
    public const TAG_HAS_BOOK = 0;
    public const TAG_PAGE = 0;
    public const TAG_TOTAL_PAGES = 0;
    public function getBook(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function getViewedPage(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function readSaveData(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function setBook(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function setViewedPage(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
}
