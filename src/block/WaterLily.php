<?php

declare(strict_types=1);

namespace pocketmine\block;

class WaterLily extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:waterlily', 'WaterLily'); }
    public function canBePlacedAt(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
}
