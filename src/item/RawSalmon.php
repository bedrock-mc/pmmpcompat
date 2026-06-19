<?php

declare(strict_types=1);

namespace pocketmine\item;

class RawSalmon extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:rawsalmon', 'RawSalmon'); }
    public function getFoodRestore(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getSaturationRestore(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
