<?php

declare(strict_types=1);

namespace pocketmine\block;

class BrownMushroomBlock extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:brownmushroomblock', 'BrownMushroomBlock'); }
    public function getDropsForCompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
}
