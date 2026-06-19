<?php

declare(strict_types=1);

namespace pocketmine\block;

class RedstoneTorch extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:redstonetorch', 'RedstoneTorch'); }
    public function getLightLevel(): int { return $this->compatMethod(__FUNCTION__, []); }
}
