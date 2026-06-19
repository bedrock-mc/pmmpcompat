<?php

declare(strict_types=1);

namespace pocketmine\item;

class GlassBottle extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:glassbottle', 'GlassBottle'); }
    public function onInteractBlock(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
}
