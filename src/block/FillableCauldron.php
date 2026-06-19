<?php

declare(strict_types=1);

namespace pocketmine\block;

class FillableCauldron extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:fillablecauldron', 'FillableCauldron'); }
    public const MAX_FILL_LEVEL = 0;
    public const MIN_FILL_LEVEL = 0;
    public function asItem(): \pocketmine\item\Item { return $this->compatMethod(__FUNCTION__, []); }
    public function getEmptySound(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getFillLevel(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getFillSound(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setFillLevel(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
