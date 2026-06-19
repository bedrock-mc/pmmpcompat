<?php

declare(strict_types=1);

namespace pocketmine\block;

class BrownMushroom extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:brownmushroom', 'BrownMushroom'); }
    public function getLightLevel(): int { return $this->compatMethod(__FUNCTION__, []); }
}
