<?php

declare(strict_types=1);

namespace pocketmine\block;

class FletchingTable extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:fletchingtable', 'FletchingTable'); }
    public function getFuelTime(): int { return $this->compatMethod(__FUNCTION__, []); }
}
