<?php

declare(strict_types=1);

namespace pocketmine\block;

class RuntimeBlockStateRegistry extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:runtimeblockstateregistry', 'RuntimeBlockStateRegistry'); }
    public const COLLISION_CUBE = 0;
    public const COLLISION_CUSTOM = 0;
    public const COLLISION_MAY_OVERFLOW = 0;
    public const COLLISION_NONE = 0;
    public function fromStateId(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getAllKnownStates(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function hasStateId(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function register(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
