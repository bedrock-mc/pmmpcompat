<?php

declare(strict_types=1);

namespace pocketmine\block;

class BrewingStand extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:brewingstand', 'BrewingStand'); }
    public function getSlots(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function hasSlot(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onScheduledUpdate(): void { $this->compatMethod(__FUNCTION__, []); }
    public function setSlot(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setSlots(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
