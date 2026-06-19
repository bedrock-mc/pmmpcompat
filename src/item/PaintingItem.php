<?php

declare(strict_types=1);

namespace pocketmine\item;

class PaintingItem extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:paintingitem', 'PaintingItem'); }
    public function onInteractBlock(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
}
