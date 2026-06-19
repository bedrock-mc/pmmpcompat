<?php

declare(strict_types=1);

namespace pocketmine\block;

class Flowable extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:flowable', 'Flowable'); }
    public function canBeFlowedInto(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function canBePlacedAt(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isSolid(): bool { return $this->compatMethod(__FUNCTION__, []); }
}
