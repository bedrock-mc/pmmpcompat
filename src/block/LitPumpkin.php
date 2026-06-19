<?php

declare(strict_types=1);

namespace pocketmine\block;

class LitPumpkin extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:litpumpkin', 'LitPumpkin'); }
    public function getLightLevel(): int { return $this->compatMethod(__FUNCTION__, []); }
}
