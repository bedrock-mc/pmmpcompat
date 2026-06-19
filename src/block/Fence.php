<?php

declare(strict_types=1);

namespace pocketmine\block;

class Fence extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:fence', 'Fence'); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getThickness(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function readStateFromWorld(): \pocketmine\block\Block { return $this->compatMethod(__FUNCTION__, []); }
}
