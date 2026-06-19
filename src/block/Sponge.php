<?php

declare(strict_types=1);

namespace pocketmine\block;

class Sponge extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:sponge', 'Sponge'); }
    public function describeBlockItemState(mixed ...$args): void { $this->compatMethod(__FUNCTION__, $args); }
    public function isWet(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setWet(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
