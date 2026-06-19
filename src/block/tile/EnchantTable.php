<?php

declare(strict_types=1);

namespace pocketmine\block\tile;

class EnchantTable extends Tile
{
    public function __construct(mixed ...$args) { parent::__construct(...$args); }
    public function getDefaultName(mixed ...$args): mixed { return $this->compatTileMethod(__FUNCTION__, $args); }
}
