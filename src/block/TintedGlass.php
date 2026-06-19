<?php

declare(strict_types=1);

namespace pocketmine\block;

class TintedGlass extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:tintedglass', 'TintedGlass'); }
    public function getLightFilter(): int { return $this->compatMethod(__FUNCTION__, []); }
}
