<?php

declare(strict_types=1);

namespace pocketmine\block;

class ChorusPlant extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:chorusplant', 'ChorusPlant'); }
    public function getDropsForCompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function readStateFromWorld(): \pocketmine\block\Block { return $this->compatMethod(__FUNCTION__, []); }
}
