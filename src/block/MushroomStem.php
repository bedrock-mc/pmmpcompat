<?php

declare(strict_types=1);

namespace pocketmine\block;

class MushroomStem extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:mushroomstem', 'MushroomStem'); }
    public function getDropsForCompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function isAffectedBySilkTouch(): bool { return $this->compatMethod(__FUNCTION__, []); }
}
