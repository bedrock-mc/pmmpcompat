<?php

declare(strict_types=1);

namespace pocketmine\block;

class SnowLayer extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:snowlayer', 'SnowLayer'); }
    public const MAX_LAYERS = 0;
    public const MIN_LAYERS = 0;
    public function canBeReplaced(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function getDropsForCompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function getLayers(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onRandomTick(): void { $this->compatMethod(__FUNCTION__, []); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function setLayers(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function ticksRandomly(): bool { return $this->compatMethod(__FUNCTION__, []); }
}
