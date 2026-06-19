<?php

declare(strict_types=1);

namespace pocketmine\item;

class LegacyStringToItemParserException extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:legacystringtoitemparserexception', 'LegacyStringToItemParserException'); }
}
