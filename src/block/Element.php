<?php

declare(strict_types=1);

namespace pocketmine\block;

class Element extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:element', 'Element'); }
    public function getAtomicWeight(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getGroup(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getSymbol(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
