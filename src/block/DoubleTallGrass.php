<?php

declare(strict_types=1);

namespace pocketmine\block;

class DoubleTallGrass extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:doubletallgrass', 'DoubleTallGrass'); }
    public function canBeReplaced(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function getDropsForIncompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
}
