<?php

declare(strict_types=1);

namespace pocketmine\block;

class Barrel extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:barrel', 'Barrel'); }
    public function getFuelTime(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function isOpen(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function setOpen(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
