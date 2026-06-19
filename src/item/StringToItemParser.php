<?php

declare(strict_types=1);

namespace pocketmine\item;

class StringToItemParser extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:stringtoitemparser', 'StringToItemParser'); }
    public function lookupAliases(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function lookupBlockAliases(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function override(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function parse(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function register(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function registerBlock(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
