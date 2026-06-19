<?php

declare(strict_types=1);

namespace pocketmine\block;

class Transparent extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:transparent', 'Transparent'); }
    public function isTransparent(): bool { return $this->compatMethod(__FUNCTION__, []); }
}
