<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

trait TallGrassTrait
{
    public function canBeReplaced(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function getDropsForIncompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function getFlameEncouragement(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getFlammability(): int { return $this->compatMethod(__FUNCTION__, []); }
}
