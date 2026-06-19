<?php

declare(strict_types=1);

namespace pocketmine\block;

class CopperDoor extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:copperdoor', 'CopperDoor'); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
}
