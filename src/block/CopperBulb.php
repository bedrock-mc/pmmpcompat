<?php

declare(strict_types=1);

namespace pocketmine\block;

class CopperBulb extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:copperbulb', 'CopperBulb'); }
    public function getLightLevel(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function togglePowered(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
