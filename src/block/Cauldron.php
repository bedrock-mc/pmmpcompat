<?php

declare(strict_types=1);

namespace pocketmine\block;

class Cauldron extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:cauldron', 'Cauldron'); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onNearbyBlockChange(): void { $this->compatMethod(__FUNCTION__, []); }
    public function writeStateToWorld(): void { $this->compatMethod(__FUNCTION__, []); }
}
