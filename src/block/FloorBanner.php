<?php

declare(strict_types=1);

namespace pocketmine\block;

class FloorBanner extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:floorbanner', 'FloorBanner'); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
}
