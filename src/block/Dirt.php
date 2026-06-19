<?php

declare(strict_types=1);

namespace pocketmine\block;

class Dirt extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:dirt', 'Dirt'); }
    public function describeBlockItemState(mixed ...$args): void { $this->compatMethod(__FUNCTION__, $args); }
    public function getDirtType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function setDirtType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
