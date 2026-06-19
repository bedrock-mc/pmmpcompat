<?php

declare(strict_types=1);

namespace pocketmine\block;

class BaseFire extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:basefire', 'BaseFire'); }
    public function canBeReplaced(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function getDropsForCompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function hasEntityCollision(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function onEntityInside(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
}
