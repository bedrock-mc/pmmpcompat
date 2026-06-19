<?php

declare(strict_types=1);

namespace pocketmine\block;

class DoublePlant extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:doubleplant', 'DoublePlant'); }
    public function getAffectedBlocks(): array { return $this->compatMethod(__FUNCTION__, []); }
    public function getDrops(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function getFlameEncouragement(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getFlammability(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function isTop(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isValidHalfPlant(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onNearbyBlockChange(): void { $this->compatMethod(__FUNCTION__, []); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function setTop(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
