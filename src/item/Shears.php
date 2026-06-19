<?php

declare(strict_types=1);

namespace pocketmine\item;

class Shears extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:shears', 'Shears'); }
    public function getBlockToolHarvestLevel(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getBlockToolType(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getMaxDurability(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onDestroyBlock(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
}
