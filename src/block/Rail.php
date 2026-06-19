<?php

declare(strict_types=1);

namespace pocketmine\block;

class Rail extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:rail', 'Rail'); }
    public function getShape(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setShape(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
