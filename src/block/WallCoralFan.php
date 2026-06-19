<?php

declare(strict_types=1);

namespace pocketmine\block;

class WallCoralFan extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:wallcoralfan', 'WallCoralFan'); }
    public function asItem(): \pocketmine\item\Item { return $this->compatMethod(__FUNCTION__, []); }
    public function onNearbyBlockChange(): void { $this->compatMethod(__FUNCTION__, []); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
}
