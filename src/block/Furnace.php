<?php

declare(strict_types=1);

namespace pocketmine\block;

class Furnace extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:furnace', 'Furnace'); }
    public function getFurnaceType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getLightLevel(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onScheduledUpdate(): void { $this->compatMethod(__FUNCTION__, []); }
}
