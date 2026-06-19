<?php

declare(strict_types=1);

namespace pocketmine\item;

class Bucket extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:bucket', 'Bucket'); }
    public function getMaxStackSize(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function onInteractBlock(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
}
