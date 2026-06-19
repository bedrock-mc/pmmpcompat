<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

interface AnalogRedstoneSignalEmitter
{
    public function getOutputSignalStrength(mixed ...$args): mixed;
    public function setOutputSignalStrength(mixed ...$args): mixed;
}
