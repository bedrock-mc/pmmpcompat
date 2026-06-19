<?php

declare(strict_types=1);

namespace pocketmine\block;

class RedstoneRepeater extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:redstonerepeater', 'RedstoneRepeater'); }
    public const MAX_DELAY = 0;
    public const MIN_DELAY = 0;
    public function getDelay(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function setDelay(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
