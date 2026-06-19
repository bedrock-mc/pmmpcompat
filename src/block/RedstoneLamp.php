<?php

declare(strict_types=1);

namespace pocketmine\block;

class RedstoneLamp extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:redstonelamp', 'RedstoneLamp'); }
    public function getLightLevel(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function isLit(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setLit(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
