<?php

declare(strict_types=1);

namespace pocketmine\block;

class SimplePressurePlate extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:simplepressureplate', 'SimplePressurePlate'); }
    public function isPressed(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setPressed(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
