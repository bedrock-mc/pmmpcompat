<?php

declare(strict_types=1);

namespace pocketmine\block;

class Wall extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:wall', 'Wall'); }
    public function getConnection(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getConnections(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isPost(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onNearbyBlockChange(): void { $this->compatMethod(__FUNCTION__, []); }
    public function setConnection(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setConnections(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setPost(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
