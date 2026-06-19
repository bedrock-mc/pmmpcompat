<?php

declare(strict_types=1);

namespace pocketmine\block;

class NetherReactor extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:netherreactor', 'NetherReactor'); }
    public function getDropsForCompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
}
