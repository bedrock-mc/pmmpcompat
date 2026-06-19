<?php

declare(strict_types=1);

namespace pocketmine\block;

class Sculk extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:sculk', 'Sculk'); }
    public function getDropsForCompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function getXpDropAmount(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isAffectedBySilkTouch(): bool { return $this->compatMethod(__FUNCTION__, []); }
}
