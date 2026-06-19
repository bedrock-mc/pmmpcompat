<?php

declare(strict_types=1);

namespace pocketmine\item;

class TieredTool extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:tieredtool', 'TieredTool'); }
    public function getEnchantability(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getFuelTime(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getMaxDurability(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getTier(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isFireProof(): bool { return $this->compatMethod(__FUNCTION__, []); }
}
