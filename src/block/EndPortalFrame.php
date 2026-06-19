<?php

declare(strict_types=1);

namespace pocketmine\block;

class EndPortalFrame extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:endportalframe', 'EndPortalFrame'); }
    public function getLightLevel(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function hasEye(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setEye(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
