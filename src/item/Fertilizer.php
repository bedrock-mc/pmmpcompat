<?php

declare(strict_types=1);

namespace pocketmine\item;

class Fertilizer extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:fertilizer', 'Fertilizer'); }
}
