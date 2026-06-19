<?php

declare(strict_types=1);

namespace pocketmine\block;

class Tripwire extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:tripwire', 'Tripwire'); }
    public function asItem(): \pocketmine\item\Item { return $this->compatMethod(__FUNCTION__, []); }
    public function isConnected(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isDisarmed(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isSuspended(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isTriggered(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setConnected(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setDisarmed(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setSuspended(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setTriggered(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
