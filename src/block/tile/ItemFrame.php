<?php

declare(strict_types=1);

namespace pocketmine\block\tile;

class ItemFrame extends Tile
{
    public function __construct(mixed ...$args) { parent::__construct(...$args); }
    public const TAG_ITEM = 0;
    public const TAG_ITEM_DROP_CHANCE = 0;
    public const TAG_ITEM_ROTATION = 0;
    public function getItem(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function getItemDropChance(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function getItemRotation(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function hasItem(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function readSaveData(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function setItem(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function setItemDropChance(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
    public function setItemRotation(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
}
