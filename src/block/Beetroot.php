<?php

declare(strict_types=1);

namespace pocketmine\block;

class Beetroot extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:beetroot', 'Beetroot'); }
    public function asItem(): \pocketmine\item\Item { return $this->compatMethod(__FUNCTION__, []); }
    public function getDropsForCompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
}
