<?php

declare(strict_types=1);

namespace pocketmine\block;

class RedMushroom extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:redmushroom', 'RedMushroom'); }
    public function onNearbyBlockChange(): void { $this->compatMethod(__FUNCTION__, []); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function ticksRandomly(): bool { return $this->compatMethod(__FUNCTION__, []); }
}
