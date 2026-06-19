<?php

declare(strict_types=1);

namespace pocketmine\block;

class Slab extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:slab', 'Slab'); }
    public function canBePlacedAt(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function getDropsForCompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function getSlabType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isTransparent(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function setSlabType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
