<?php

declare(strict_types=1);

namespace pocketmine\item;

class EndCrystal extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:endcrystal', 'EndCrystal'); }
    public function onInteractBlock(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
}
