<?php

declare(strict_types=1);

namespace pocketmine\block;

class Chain extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:chain', 'Chain'); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
