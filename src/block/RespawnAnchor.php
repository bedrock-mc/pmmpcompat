<?php

declare(strict_types=1);

namespace pocketmine\block;

class RespawnAnchor extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:respawnanchor', 'RespawnAnchor'); }
    public function getCharges(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getLightLevel(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function setCharges(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
