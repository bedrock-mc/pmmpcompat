<?php

declare(strict_types=1);

namespace pocketmine\block;

class Wood extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:wood', 'Wood'); }
    public function describeBlockItemState(mixed ...$args): void { $this->compatMethod(__FUNCTION__, $args); }
    public function getFlameEncouragement(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getFlammability(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getFuelTime(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function isStripped(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function setStripped(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
