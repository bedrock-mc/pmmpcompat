<?php

declare(strict_types=1);

namespace pocketmine\block;

class AmethystCluster extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:amethystcluster', 'AmethystCluster'); }
    public const STAGE_CLUSTER = 0;
    public const STAGE_LARGE_BUD = 0;
    public const STAGE_MEDIUM_BUD = 0;
    public const STAGE_SMALL_BUD = 0;
    public function describeBlockItemState(mixed ...$args): void { $this->compatMethod(__FUNCTION__, $args); }
    public function getDropsForCompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function getDropsForIncompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function getLightLevel(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getStage(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isAffectedBySilkTouch(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function onNearbyBlockChange(): void { $this->compatMethod(__FUNCTION__, []); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function setStage(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
