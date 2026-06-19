<?php

declare(strict_types=1);

namespace pocketmine\block;

class NetherPortal extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:netherportal', 'NetherPortal'); }
    public function getAxis(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getDrops(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function getLightLevel(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isSolid(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function onEntityInside(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function setAxis(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
