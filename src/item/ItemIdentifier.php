<?php

declare(strict_types=1);

namespace pocketmine\item;

class ItemIdentifier extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:itemidentifier', 'ItemIdentifier'); }
    public static function fromBlock(mixed ...$args): mixed { return self::compatStaticMethod(__FUNCTION__, $args); }
    public function getTypeId(): string { return $this->compatMethod(__FUNCTION__, []); }
}
