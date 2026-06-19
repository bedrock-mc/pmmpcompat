<?php

declare(strict_types=1);

namespace pocketmine\block;

class Torch extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:torch', 'Torch'); }
    public function getFacing(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getLightLevel(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function onNearbyBlockChange(): void { $this->compatMethod(__FUNCTION__, []); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function setFacing(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
