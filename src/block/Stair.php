<?php

declare(strict_types=1);

namespace pocketmine\block;

class Stair extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:stair', 'Stair'); }
    public function getShape(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isUpsideDown(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function readStateFromWorld(): \pocketmine\block\Block { return $this->compatMethod(__FUNCTION__, []); }
    public function setShape(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setUpsideDown(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
