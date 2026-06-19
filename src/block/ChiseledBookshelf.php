<?php

declare(strict_types=1);

namespace pocketmine\block;

class ChiseledBookshelf extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:chiseledbookshelf', 'ChiseledBookshelf'); }
    public function getDropsForCompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function getLastInteractedSlot(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getSlots(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function hasSlot(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isAffectedBySilkTouch(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function readStateFromWorld(): \pocketmine\block\Block { return $this->compatMethod(__FUNCTION__, []); }
    public function setLastInteractedSlot(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setSlot(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setSlots(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function writeStateToWorld(): void { $this->compatMethod(__FUNCTION__, []); }
}
