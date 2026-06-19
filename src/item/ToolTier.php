<?php

declare(strict_types=1);

namespace pocketmine\item;

enum ToolTier
{
    case STUB;
    public function getBaseAttackPoints(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getBaseEfficiency(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getEnchantability(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getHarvestLevel(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getMaxDurability(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
