<?php

declare(strict_types=1);

namespace pocketmine\block;

class Trapdoor extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:trapdoor', 'Trapdoor'); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isOpen(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isTop(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function setOpen(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setTop(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
