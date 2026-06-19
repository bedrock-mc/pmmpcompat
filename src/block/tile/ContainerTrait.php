<?php

declare(strict_types=1);

namespace pocketmine\block\tile;

trait ContainerTrait
{
    public function canOpenWith(mixed ...$args): mixed { return method_exists($this, 'compatTileMethod') ? $this->compatTileMethod(__FUNCTION__, $args) : null; }
    public function getRealInventory(mixed ...$args): mixed { return method_exists($this, 'compatTileMethod') ? $this->compatTileMethod(__FUNCTION__, $args) : null; }
}
