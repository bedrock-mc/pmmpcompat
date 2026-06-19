<?php

declare(strict_types=1);

namespace pocketmine\block;

class OminousFloorBanner extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:ominousfloorbanner', 'OminousFloorBanner'); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
}
