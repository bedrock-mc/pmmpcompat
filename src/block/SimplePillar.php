<?php

declare(strict_types=1);

namespace pocketmine\block;

class SimplePillar extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:simplepillar', 'SimplePillar'); }
}
