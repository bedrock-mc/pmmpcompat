<?php

declare(strict_types=1);

namespace pocketmine\item;

class Clownfish extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:clownfish', 'Clownfish'); }
    public function getFoodRestore(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getSaturationRestore(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
