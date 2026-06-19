<?php

declare(strict_types=1);

namespace pocketmine\block;

class BigDripleafStem extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:bigdripleafstem', 'BigDripleafStem'); }
    public function asItem(): \pocketmine\item\Item { return $this->compatMethod(__FUNCTION__, []); }
}
