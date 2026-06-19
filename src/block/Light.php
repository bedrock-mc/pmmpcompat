<?php

declare(strict_types=1);

namespace pocketmine\block;

class Light extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:light', 'Light'); }
    public const MAX_LIGHT_LEVEL = 0;
    public const MIN_LIGHT_LEVEL = 0;
    public function canBePlacedAt(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function canBeReplaced(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function describeBlockItemState(mixed ...$args): void { $this->compatMethod(__FUNCTION__, $args); }
    public function getLightLevel(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function setLightLevel(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
