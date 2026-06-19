<?php

declare(strict_types=1);

namespace pocketmine\block;

class Sapling extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:sapling', 'Sapling'); }
    public function getFuelTime(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function isReady(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onRandomTick(): void { $this->compatMethod(__FUNCTION__, []); }
    public function setReady(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function ticksRandomly(): bool { return $this->compatMethod(__FUNCTION__, []); }
}
