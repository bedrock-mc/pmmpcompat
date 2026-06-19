<?php

declare(strict_types=1);

namespace pocketmine\block\tile;

class Cauldron extends Tile
{
    public function __construct(mixed ...$args) { parent::__construct(...$args); }
    public function getCustomWaterColor(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function getPotionItem(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function getRenderUpdateBugWorkaroundStateProperties(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function readSaveData(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function setCustomWaterColor(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function setPotionItem(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
}
