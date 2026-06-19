<?php

declare(strict_types=1);

namespace pocketmine\block;

class PinkPetals extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:pinkpetals', 'PinkPetals'); }
    public const MAX_COUNT = 0;
    public const MIN_COUNT = 0;
    public function canBePlacedAt(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function getCount(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getDropsForCompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function getFlameEncouragement(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getFlammability(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function setCount(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
