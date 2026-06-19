<?php

declare(strict_types=1);

namespace pocketmine\item;

class Record extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:record', 'Record'); }
    public function getMaxStackSize(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getRecordType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
