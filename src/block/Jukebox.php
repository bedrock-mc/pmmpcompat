<?php

declare(strict_types=1);

namespace pocketmine\block;

class Jukebox extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:jukebox', 'Jukebox'); }
    public function ejectRecord(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getDropsForCompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function getFuelTime(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getRecord(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function insertRecord(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onBreak(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function readStateFromWorld(): \pocketmine\block\Block { return $this->compatMethod(__FUNCTION__, []); }
    public function startSound(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function stopSound(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function writeStateToWorld(): void { $this->compatMethod(__FUNCTION__, []); }
}
