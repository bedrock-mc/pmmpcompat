<?php

declare(strict_types=1);

namespace pocketmine\block;

class UnderwaterTorch extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:underwatertorch', 'UnderwaterTorch'); }
    public function canBeFlowedInto(): bool { return $this->compatMethod(__FUNCTION__, []); }
}
