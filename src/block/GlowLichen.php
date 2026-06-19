<?php

declare(strict_types=1);

namespace pocketmine\block;

class GlowLichen extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:glowlichen', 'GlowLichen'); }
    public function canBeReplaced(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function getDrops(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function getFlameEncouragement(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getFlammability(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getLightLevel(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isSolid(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
}
