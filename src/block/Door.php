<?php

declare(strict_types=1);

namespace pocketmine\block;

class Door extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:door', 'Door'); }
    public function getAffectedBlocks(): array { return $this->compatMethod(__FUNCTION__, []); }
    public function getDrops(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isHingeRight(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isOpen(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isSolid(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function isTop(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onNearbyBlockChange(): void { $this->compatMethod(__FUNCTION__, []); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function readStateFromWorld(): \pocketmine\block\Block { return $this->compatMethod(__FUNCTION__, []); }
    public function setHingeRight(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setOpen(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setTop(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
