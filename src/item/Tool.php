<?php

declare(strict_types=1);

namespace pocketmine\item;

class Tool extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:tool', 'Tool'); }
    public function getMaxStackSize(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getMiningEfficiency(bool $isCorrectTool): float { return $this->compatMethod(__FUNCTION__, [$isCorrectTool]); }
}
