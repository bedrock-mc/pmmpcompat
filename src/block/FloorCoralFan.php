<?php

declare(strict_types=1);

namespace pocketmine\block;

class FloorCoralFan extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:floorcoralfan', 'FloorCoralFan'); }
    public function asItem(): \pocketmine\item\Item { return $this->compatMethod(__FUNCTION__, []); }
    public function getAxis(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function setAxis(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
