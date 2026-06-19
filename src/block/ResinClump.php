<?php

declare(strict_types=1);

namespace pocketmine\block;

class ResinClump extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:resinclump', 'ResinClump'); }
    public function canBeReplaced(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isSolid(): bool { return $this->compatMethod(__FUNCTION__, []); }
}
