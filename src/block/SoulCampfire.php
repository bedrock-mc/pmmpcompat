<?php

declare(strict_types=1);

namespace pocketmine\block;

class SoulCampfire extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:soulcampfire', 'SoulCampfire'); }
    public function getDropsForCompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function getLightLevel(): int { return $this->compatMethod(__FUNCTION__, []); }
}
