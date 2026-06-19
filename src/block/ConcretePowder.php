<?php

declare(strict_types=1);

namespace pocketmine\block;

class ConcretePowder extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:concretepowder', 'ConcretePowder'); }
    public function onNearbyBlockChange(): void { $this->compatMethod(__FUNCTION__, []); }
    public function tickFalling(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
