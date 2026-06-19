<?php

declare(strict_types=1);

namespace pocketmine\block;

class Bookshelf extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:bookshelf', 'Bookshelf'); }
    public function getDropsForCompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function getFlameEncouragement(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getFlammability(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getFuelTime(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function isAffectedBySilkTouch(): bool { return $this->compatMethod(__FUNCTION__, []); }
}
