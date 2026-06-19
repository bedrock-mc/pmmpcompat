<?php

declare(strict_types=1);

namespace pocketmine\block;

class BaseRail extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:baserail', 'BaseRail'); }
    public function onNearbyBlockChange(): void { $this->compatMethod(__FUNCTION__, []); }
    public function onPostPlace(): void { $this->compatMethod(__FUNCTION__, []); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
}
