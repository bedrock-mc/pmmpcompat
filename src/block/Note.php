<?php

declare(strict_types=1);

namespace pocketmine\block;

class Note extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:note', 'Note'); }
    public const MAX_PITCH = 0;
    public const MIN_PITCH = 0;
    public function getFuelTime(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getPitch(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function readStateFromWorld(): \pocketmine\block\Block { return $this->compatMethod(__FUNCTION__, []); }
    public function setPitch(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function writeStateToWorld(): void { $this->compatMethod(__FUNCTION__, []); }
}
