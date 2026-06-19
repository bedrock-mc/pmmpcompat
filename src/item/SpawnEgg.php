<?php

declare(strict_types=1);

namespace pocketmine\item;

class SpawnEgg extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:spawnegg', 'SpawnEgg'); }
    public function onInteractBlock(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
}
