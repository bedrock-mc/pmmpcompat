<?php

declare(strict_types=1);

namespace pocketmine\block;

class TripwireHook extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:tripwirehook', 'TripwireHook'); }
    public function isConnected(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isPowered(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function setConnected(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setPowered(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
