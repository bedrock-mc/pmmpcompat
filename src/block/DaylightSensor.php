<?php

declare(strict_types=1);

namespace pocketmine\block;

class DaylightSensor extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:daylightsensor', 'DaylightSensor'); }
    public function getFuelTime(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isInverted(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onScheduledUpdate(): void { $this->compatMethod(__FUNCTION__, []); }
    public function setInverted(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
