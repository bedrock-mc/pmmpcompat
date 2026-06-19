<?php

declare(strict_types=1);

namespace pocketmine\block;

class DragonEgg extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:dragonegg', 'DragonEgg'); }
    public function getLightLevel(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onAttack(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function teleport(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
