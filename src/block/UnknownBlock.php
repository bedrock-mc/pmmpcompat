<?php

declare(strict_types=1);

namespace pocketmine\block;

class UnknownBlock extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:unknownblock', 'UnknownBlock'); }
    public function canBePlaced(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function describeBlockItemState(mixed ...$args): void { $this->compatMethod(__FUNCTION__, $args); }
    public function getDrops(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
}
