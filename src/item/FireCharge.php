<?php

declare(strict_types=1);

namespace pocketmine\item;

class FireCharge extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:firecharge', 'FireCharge'); }
    public function onInteractBlock(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
}
