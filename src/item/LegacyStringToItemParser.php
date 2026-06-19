<?php

declare(strict_types=1);

namespace pocketmine\item;

class LegacyStringToItemParser extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:legacystringtoitemparser', 'LegacyStringToItemParser'); }
    public function addMapping(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getMappings(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function parse(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
