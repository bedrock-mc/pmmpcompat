<?php

declare(strict_types=1);

namespace pocketmine\block;

class Thin extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:thin', 'Thin'); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function readStateFromWorld(): \pocketmine\block\Block { return $this->compatMethod(__FUNCTION__, []); }
}
