<?php

declare(strict_types=1);

namespace pocketmine\block\tile;

trait NameableTrait
{
    public function addAdditionalSpawnData(mixed ...$args): mixed { return method_exists($this, 'compatTileMethod') ? $this->compatTileMethod(__FUNCTION__, $args) : null; }
    public function copyDataFromItem(mixed ...$args): mixed { return method_exists($this, 'compatTileMethod') ? $this->compatTileMethod(__FUNCTION__, $args) : null; }
    public function getDefaultName(mixed ...$args): mixed { return method_exists($this, 'compatTileMethod') ? $this->compatTileMethod(__FUNCTION__, $args) : null; }
    public function getName(mixed ...$args): mixed { return method_exists($this, 'compatTileMethod') ? $this->compatTileMethod(__FUNCTION__, $args) : null; }
    public function hasName(mixed ...$args): mixed { return method_exists($this, 'compatTileMethod') ? $this->compatTileMethod(__FUNCTION__, $args) : null; }
    public function setName(mixed ...$args): mixed { return method_exists($this, 'compatTileMethod') ? $this->compatTileMethod(__FUNCTION__, $args) : null; }
}
