<?php

declare(strict_types=1);

namespace pocketmine\item;

class Dye extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:dye', 'Dye'); }
    public function getColor(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setColor(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
