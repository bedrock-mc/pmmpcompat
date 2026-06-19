<?php

declare(strict_types=1);

namespace pocketmine\block;

class RedstoneComparator extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:redstonecomparator', 'RedstoneComparator'); }
    public function isSubtractMode(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function readStateFromWorld(): \pocketmine\block\Block { return $this->compatMethod(__FUNCTION__, []); }
    public function setSubtractMode(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function writeStateToWorld(): void { $this->compatMethod(__FUNCTION__, []); }
}
