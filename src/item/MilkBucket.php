<?php

declare(strict_types=1);

namespace pocketmine\item;

class MilkBucket extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:milkbucket', 'MilkBucket'); }
    public function canStartUsingItem(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getAdditionalEffects(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getMaxStackSize(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getResidue(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onConsume(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
