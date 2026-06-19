<?php

declare(strict_types=1);

namespace pocketmine\block;

class Opaque extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:opaque', 'Opaque'); }
    public function isSolid(): bool { return $this->compatMethod(__FUNCTION__, []); }
}
