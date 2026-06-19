<?php

declare(strict_types=1);

namespace pocketmine\item;

class NameTag extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:nametag', 'NameTag'); }
    public function onInteractEntity(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
}
