<?php

declare(strict_types=1);

namespace pocketmine\block;

class BaseCoral extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:basecoral', 'BaseCoral'); }
    public function getDropsForCompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isAffectedBySilkTouch(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function isSolid(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function onNearbyBlockChange(): void { $this->compatMethod(__FUNCTION__, []); }
    public function onScheduledUpdate(): void { $this->compatMethod(__FUNCTION__, []); }
}
