<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

trait AnalogRedstoneSignalEmitterTrait
{
    public function getOutputSignalStrength(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setOutputSignalStrength(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
