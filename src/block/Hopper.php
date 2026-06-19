<?php

declare(strict_types=1);

namespace pocketmine\block;

class Hopper extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:hopper', 'Hopper'); }
    public function getFacing(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onScheduledUpdate(): void { $this->compatMethod(__FUNCTION__, []); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function setFacing(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
