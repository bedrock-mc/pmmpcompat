<?php

declare(strict_types=1);

namespace pocketmine\block;

class Lever extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:lever', 'Lever'); }
    public function getFacing(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isActivated(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onNearbyBlockChange(): void { $this->compatMethod(__FUNCTION__, []); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function setActivated(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setFacing(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
