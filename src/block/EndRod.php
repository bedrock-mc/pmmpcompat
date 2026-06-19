<?php

declare(strict_types=1);

namespace pocketmine\block;

class EndRod extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:endrod', 'EndRod'); }
    public function getLightLevel(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function isSolid(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
}
