<?php

declare(strict_types=1);

namespace pocketmine\block;

class WallBanner extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:wallbanner', 'WallBanner'); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
}
