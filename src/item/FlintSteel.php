<?php

declare(strict_types=1);

namespace pocketmine\item;

class FlintSteel extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:flintsteel', 'FlintSteel'); }
    public function getMaxDurability(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onInteractBlock(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
}
